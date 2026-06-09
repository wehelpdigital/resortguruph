/**
 * Iframe-side runtime for the mother super-admin Live Editor.
 *
 * Runs ONLY when the page is inside an iframe AND the frontend's
 * KeywordPageController validated the HMAC `_lt` token (which sets
 * window.__rgLiveEdit = {pageId, slug, ownerType}). On user
 * actions (edit, delete, move via drag, add-here) we emit a
 * postMessage to window.parent; the mother super-admin's
 * live-edit view listens and dispatches AJAX calls + modal opens.
 *
 * The frontend does NOT make any DB writes. All mutations go back
 * through the mother app's existing
 * /resort-guru-blocks-{save,delete,reorder} endpoints.
 */
(function () {
    'use strict';

    // Only run in an iframe — direct visits should never enable chrome.
    if (window === window.parent) return;
    if (!window.__rgLiveEdit) return;

    var cfg = window.__rgLiveEdit;
    document.body.classList.add('rg-live-edit-on');

    // Top-left banner that says "LIVE EDIT" so the admin can see
    // at a glance that they're in the editing mode.
    var banner = document.createElement('div');
    banner.className = 'rg-live-banner';
    banner.textContent = 'Live Edit';
    document.body.appendChild(banner);

    // Toolbar template per block. Drag handle = the type badge.
    function toolbarFor(blockId, blockType) {
        var t = document.createElement('div');
        t.className = 'rg-live-toolbar';
        t.contentEditable = 'false';
        t.innerHTML =
            '<span class="rg-live-type-badge" title="Drag to reorder">'
            + escapeHtml(blockType) + '</span>'
            + '<button data-action="edit" title="Edit this block">Edit</button>'
            + '<button data-action="move-up" title="Move up">&uarr;</button>'
            + '<button data-action="move-down" title="Move down">&darr;</button>'
            + '<button data-action="delete" title="Delete block">Delete</button>';
        t.addEventListener('click', function (e) {
            var btn = e.target.closest('button');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            emit(btn.dataset.action, {blockId: blockId, blockType: blockType});
        });
        return t;
    }

    // "+ Add block here" rail rendered between each adjacent pair of
    // blocks (and once before the first / once after the last).
    function addBtn(afterId) {
        var b = document.createElement('button');
        b.type = 'button';
        b.className = 'rg-live-add-btn';
        b.textContent = '+ Add block here';
        b.dataset.afterBlockId = afterId == null ? '' : String(afterId);
        b.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            emit('add', {afterBlockId: afterId});
        });
        return b;
    }

    function emit(action, payload) {
        var msg = Object.assign(
            {rgLiveEdit: true, action: action, pageId: cfg.pageId, slug: cfg.slug},
            payload || {}
        );
        window.parent.postMessage(msg, '*');
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function (c) {
            return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[c];
        });
    }

    function setup() {
        var blocks = Array.prototype.slice.call(
            document.querySelectorAll('.rg-live-block')
        );
        if (!blocks.length) {
            emit('ready', {blockCount: 0, warning: 'no_blocks_found'});
            return;
        }

        blocks.forEach(function (block) {
            var id = parseInt(block.dataset.rgBlockId, 10);
            var type = block.dataset.rgBlockType || 'block';
            block.prepend(toolbarFor(id, type));
        });

        // Find the common parent and attach SortableJS. All blocks
        // are direct siblings inside whatever container the keyword-
        // page view wraps them in. Walk up from the first block's
        // parent so we pick the right container regardless of how
        // the view nests them.
        var container = blocks[0].parentElement;

        // Insert add-block rails between siblings.
        var prevId = null;
        Array.prototype.slice.call(container.children).forEach(function (child) {
            if (!child.classList.contains('rg-live-block')) return;
            container.insertBefore(addBtn(prevId), child);
            prevId = parseInt(child.dataset.rgBlockId, 10);
        });
        container.appendChild(addBtn(prevId)); // trailing "add at end"

        // SortableJS drag-drop. We restrict draggable to .rg-live-block
        // so the inserted .rg-live-add-btn rails stay anchored.
        if (window.Sortable) {
            window.Sortable.create(container, {
                animation: 200,
                draggable: '.rg-live-block',
                handle: '.rg-live-type-badge',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function () {
                    var order = Array.prototype.slice
                        .call(container.querySelectorAll('.rg-live-block'))
                        .map(function (b) { return parseInt(b.dataset.rgBlockId, 10); });
                    emit('reorder', {order: order});
                }
            });
        }

        emit('ready', {blockCount: blocks.length});
    }

    // SortableJS loads from CDN with `defer` so it may finish after
    // DOMContentLoaded. Poll briefly for window.Sortable; if it never
    // appears (CDN down / blocked) we still wire up edit/delete/add
    // and just skip the drag handlers.
    function waitForSortable(maxMs, cb) {
        var start = Date.now();
        (function tick() {
            if (window.Sortable || Date.now() - start > maxMs) return cb();
            setTimeout(tick, 50);
        })();
    }

    function boot() {
        waitForSortable(2000, setup);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
