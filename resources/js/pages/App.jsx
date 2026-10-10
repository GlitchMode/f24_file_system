import { useCallback, useEffect, useRef, useState } from 'react';
import ContextMenu from '../components/ContextMenu';
import CreateDialog from '../components/CreateDialog';
import {
    browseFolder,
    createFile,
    createFolder,
    deleteFile,
    deleteFolder,
    searchFiles,
    suggestFiles,
} from '../services/api';

export default function App() {
    const [contents, setContents] = useState({ folder: null, folders: [], files: [] });
    const [breadcrumbs, setBreadcrumbs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState('');
    const [menu, setMenu] = useState(null);
    const [createType, setCreateType] = useState(null);
    const [query, setQuery] = useState('');
    const [scope, setScope] = useState('all');
    const [suggestions, setSuggestions] = useState([]);
    const [showSuggestions, setShowSuggestions] = useState(false);
    const [searchResults, setSearchResults] = useState(null);
    const searchBox = useRef(null);

    const currentFolderId = contents.folder?.id ?? null;

    const loadFolder = useCallback(async (folderId = null) => {
        setLoading(true);
        setError('');

        try {
            const data = await browseFolder(folderId);
            setContents(data);
            setSearchResults(null);
        } catch (requestError) {
            setError(requestError.message);
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        loadFolder();
    }, [loadFolder]);

    useEffect(() => {
        function closeOverlays(event) {
            setMenu(null);

            if (!searchBox.current?.contains(event.target)) {
                setShowSuggestions(false);
            }
        }

        window.addEventListener('click', closeOverlays);
        window.addEventListener('resize', closeOverlays);

        return () => {
            window.removeEventListener('click', closeOverlays);
            window.removeEventListener('resize', closeOverlays);
        };
    }, []);

    useEffect(() => {
        if (!query.trim()) {
            setSuggestions([]);
            setShowSuggestions(false);
            return undefined;
        }

        const controller = new AbortController();
        const timeout = window.setTimeout(async () => {
            try {
                const data = await suggestFiles(query.trim(), controller.signal);
                setSuggestions(data);
                setShowSuggestions(true);
            } catch (requestError) {
                if (requestError.name !== 'AbortError') {
                    setError(requestError.message);
                }
            }
        }, 200);

        return () => {
            window.clearTimeout(timeout);
            controller.abort();
        };
    }, [query]);

    async function openFolder(folder) {
        setBreadcrumbs((items) => [...items, folder]);
        await loadFolder(folder.id);
    }

    async function openBreadcrumb(folder, index) {
        setBreadcrumbs((items) => items.slice(0, index + 1));
        await loadFolder(folder.id);
    }

    async function openRoot() {
        setBreadcrumbs([]);
        await loadFolder();
    }

    function openWorkspaceMenu(event) {
        if (event.target.closest('.file-item')) {
            return;
        }

        event.preventDefault();
        setMenu({ type: 'workspace', x: event.clientX, y: event.clientY });
    }

    function openItemMenu(event, type, item) {
        event.preventDefault();
        event.stopPropagation();
        setMenu({ type, item, x: event.clientX, y: event.clientY });
    }

    function beginCreate(type) {
        setMenu(null);
        setCreateType(type);
    }

    async function handleCreate(name) {
        setBusy(true);
        setError('');

        try {
            if (createType === 'folder') {
                await createFolder(name, currentFolderId);
            } else {
                await createFile(name, currentFolderId);
            }

            setCreateType(null);
            await loadFolder(currentFolderId);
        } catch (requestError) {
            setError(requestError.message);
        } finally {
            setBusy(false);
        }
    }

    async function handleDelete() {
        const target = menu;
        setMenu(null);

        const cascadeWarning = target?.type === 'folder' ? ' Its contents will also be deleted.' : '';

        if (!target || !window.confirm(`Delete ${target.item.name}?${cascadeWarning}`)) {
            return;
        }

        setBusy(true);
        setError('');

        try {
            if (target.type === 'folder') {
                await deleteFolder(target.item.id);
            } else {
                await deleteFile(target.item.id);
            }

            await loadFolder(currentFolderId);
        } catch (requestError) {
            setError(requestError.message);
        } finally {
            setBusy(false);
        }
    }

    async function runSearch(event, selectedName = null) {
        event?.preventDefault();
        const name = selectedName ?? query.trim();

        if (!name) {
            return;
        }

        setQuery(name);
        setShowSuggestions(false);
        setLoading(true);
        setError('');

        try {
            const data = await searchFiles(name, currentFolderId, scope === 'current');
            setSearchResults(data);
        } catch (requestError) {
            setError(requestError.message);
        } finally {
            setLoading(false);
        }
    }

    function clearSearch() {
        setQuery('');
        setSuggestions([]);
        setSearchResults(null);
    }

    const visibleFiles = searchResults ?? contents.files;

    return (
        <div className="file-manager" onContextMenu={openWorkspaceMenu}>
            <header className="topbar">
                <div>
                    <p className="eyebrow">F24 workspace</p>
                    <h1>File Manager</h1>
                </div>

                <form className="search" onSubmit={runSearch} ref={searchBox}>
                    <div className="search-input-wrap">
                        <span className="search-icon" aria-hidden="true">⌕</span>
                        <input
                            type="search"
                            value={query}
                            onChange={(event) => setQuery(event.target.value)}
                            onFocus={() => suggestions.length && setShowSuggestions(true)}
                            placeholder="Search exact file name"
                            aria-label="Search files"
                            autoComplete="off"
                        />
                        {showSuggestions && (
                            <div className="suggestions">
                                {suggestions.length ? suggestions.map((file) => (
                                    <button key={file.id} type="button" onClick={(event) => runSearch(event, file.name)}>
                                        <span className="mini-file" aria-hidden="true">▤</span>
                                        <span>{file.name}</span>
                                    </button>
                                )) : (
                                    <p>No suggestions</p>
                                )}
                            </div>
                        )}
                    </div>
                    <select value={scope} onChange={(event) => setScope(event.target.value)} aria-label="Search scope">
                        <option value="all">All files</option>
                        <option value="current">Current folder</option>
                    </select>
                    <button className="search-button" type="submit">Search</button>
                </form>
            </header>

            <section className="toolbar">
                <nav className="breadcrumbs" aria-label="Breadcrumb">
                    <button type="button" onClick={openRoot}>My files</button>
                    {breadcrumbs.map((folder, index) => (
                        <span key={folder.id}>
                            <span className="separator">/</span>
                            <button type="button" onClick={() => openBreadcrumb(folder, index)}>{folder.name}</button>
                        </span>
                    ))}
                </nav>

                <div className="toolbar-actions">
                    {searchResults && (
                        <button className="secondary-button" type="button" onClick={clearSearch}>Clear search</button>
                    )}
                    <button className="primary-button" type="button" onClick={() => beginCreate('folder')}>New folder</button>
                    <button className="secondary-button" type="button" onClick={() => beginCreate('file')}>New file</button>
                </div>
            </section>

            {error && <div className="error-banner" role="alert">{error}</div>}

            <main className="workspace" aria-busy={loading || busy}>
                <div className="workspace-heading">
                    <div>
                        <h2>{searchResults ? `Search results for “${query}”` : (contents.folder?.name ?? 'My files')}</h2>
                        <p>{searchResults ? `${searchResults.length} exact match${searchResults.length === 1 ? '' : 'es'}` : 'Right-click empty space to create an item. Right-click an item to delete it.'}</p>
                    </div>
                    {(loading || busy) && <span className="loading">Working…</span>}
                </div>

                <div className="file-grid">
                    {!searchResults && contents.folders.map((folder) => (
                        <button
                            className="file-item"
                            type="button"
                            key={`folder-${folder.id}`}
                            onDoubleClick={() => openFolder(folder)}
                            onContextMenu={(event) => openItemMenu(event, 'folder', folder)}
                        >
                            <span className="folder-icon" aria-hidden="true"></span>
                            <span className="item-name">{folder.name}</span>
                            <span className="item-type">Folder</span>
                        </button>
                    ))}

                    {visibleFiles.map((file) => (
                        <button
                            className="file-item"
                            type="button"
                            key={`file-${file.id}`}
                            onContextMenu={(event) => openItemMenu(event, 'file', file)}
                        >
                            <span className="document-icon" aria-hidden="true"></span>
                            <span className="item-name">{file.name}</span>
                            <span className="item-type">{searchResults && file.folder_id ? `Folder #${file.folder_id}` : 'File'}</span>
                        </button>
                    ))}

                    {!loading && (searchResults !== null || !contents.folders.length) && !visibleFiles.length && (
                        <div className="empty-state">
                            <span aria-hidden="true">◇</span>
                            <h3>{searchResults ? 'No matching files' : 'This folder is empty'}</h3>
                            <p>{searchResults ? 'Try another exact file name or search scope.' : 'Right-click here to create your first folder or file.'}</p>
                        </div>
                    )}
                </div>
            </main>

            <ContextMenu menu={menu} onCreate={beginCreate} onDelete={handleDelete} />
            <CreateDialog type={createType} busy={busy} onClose={() => !busy && setCreateType(null)} onSubmit={handleCreate} />
        </div>
    );
}
