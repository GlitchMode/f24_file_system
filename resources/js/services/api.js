const jsonHeaders = {
    Accept: 'application/json',
    'Content-Type': 'application/json',
};

async function request(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: { ...jsonHeaders, ...options.headers },
    });

    if (response.status === 204) {
        return null;
    }

    const data = await response.json();

    if (!response.ok) {
        const validationMessage = data.errors
            ? Object.values(data.errors).flat().join(' ')
            : null;

        throw new Error(validationMessage || data.message || 'The request could not be completed.');
    }

    return data;
}

export function browseFolder(folderId = null) {
    return request(folderId ? `/api/folders/${folderId}` : '/api/folders');
}

export function createFolder(name, parentId) {
    return request('/api/folders', {
        method: 'POST',
        body: JSON.stringify({ name, parent_id: parentId }),
    });
}

export function createFile(name, folderId) {
    return request('/api/files', {
        method: 'POST',
        body: JSON.stringify({ name, folder_id: folderId }),
    });
}

export function deleteFolder(folderId) {
    return request(`/api/folders/${folderId}`, { method: 'DELETE' });
}

export function deleteFile(fileId) {
    return request(`/api/files/${fileId}`, { method: 'DELETE' });
}

export function searchFiles(name, folderId, scoped) {
    const params = new URLSearchParams({ name });

    if (scoped) {
        params.set('folder_id', folderId ?? '');
    }

    return request(`/api/files/search?${params.toString()}`);
}

export function suggestFiles(query, signal) {
    const params = new URLSearchParams({ q: query });

    return request(`/api/files/suggestions?${params.toString()}`, { signal });
}
