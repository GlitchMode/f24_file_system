import { useEffect, useState } from 'react';

export default function CreateDialog({ type, busy, onClose, onSubmit }) {
    const [name, setName] = useState('');

    useEffect(() => {
        setName('');
    }, [type]);

    if (!type) {
        return null;
    }

    function handleSubmit(event) {
        event.preventDefault();

        if (name.trim()) {
            onSubmit(name.trim());
        }
    }

    return (
        <div className="dialog-backdrop" role="presentation" onMouseDown={onClose}>
            <form className="dialog" onSubmit={handleSubmit} onMouseDown={(event) => event.stopPropagation()}>
                <h2>Create {type}</h2>
                <label htmlFor="item-name">Name</label>
                <input
                    id="item-name"
                    autoFocus
                    value={name}
                    onChange={(event) => setName(event.target.value)}
                    placeholder={type === 'folder' ? 'New folder' : 'example.txt'}
                />
                <div className="dialog-actions">
                    <button className="secondary-button" type="button" onClick={onClose} disabled={busy}>Cancel</button>
                    <button className="primary-button" type="submit" disabled={busy || !name.trim()}>
                        {busy ? 'Creating…' : 'Create'}
                    </button>
                </div>
            </form>
        </div>
    );
}
