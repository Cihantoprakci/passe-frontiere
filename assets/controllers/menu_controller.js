import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['nav', 'button', 'label'];

    toggle() {
        const open = this.navTarget.classList.toggle('is-open');
        this.buttonTarget.setAttribute('aria-expanded', open);
        this.labelTarget.textContent = open ? 'Fermer' : 'Menu';
    }
}
