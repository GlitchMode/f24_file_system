export default function ContextMenu({ menu, onCreate, onDelete }) {
    if (!menu) {
        return null;
    }

    return (
        <div
            className="context-menu"
            style={{ left: menu.x, top: menu.y }}
            role="menu"
            onClick={(event) => event.stopPropagation()}
        >
            {menu.type === 'workspace' ? (
                <>
                    <button type="button" role="menuitem" onClick={() => onCreate('folder')}>New folder</button>
                    <button type="button" role="menuitem" onClick={() => onCreate('file')}>New file</button>
                </>
            ) : (
                <button className="danger" type="button" role="menuitem" onClick={onDelete}>
                    Delete {menu.type}
                </button>
            )}
        </div>
    );
}
