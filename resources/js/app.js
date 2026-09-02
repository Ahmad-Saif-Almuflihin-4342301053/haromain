import './bootstrap';
import 'flowbite'
import Viewer from 'viewerjs'
import 'viewerjs/dist/viewer.css'


function initViewer() {
    document.querySelectorAll('.image-viewer').forEach(el => {
        if (el.dataset.viewerInit) return
        el.dataset.viewerInit = true

        const viewer = new Viewer(el, {
            hidden() {
                // remove focus from viewer buttons
                document.activeElement?.blur()
            }
        })
    })
}

document.addEventListener('DOMContentLoaded', initViewer)
document.addEventListener('livewire:navigated', initViewer)
