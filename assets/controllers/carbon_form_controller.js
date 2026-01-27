import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['category', 'section'];

    connect() {
        this.toggle();
    }

    toggle() {
        const selected = this.categoryTarget.value;
        this.sectionTargets.forEach((section) => {
            const matches = section.dataset.category === selected;
            section.classList.toggle('is-hidden', !matches);

            section.querySelectorAll('input, select, textarea').forEach((field) => {
                field.toggleAttribute('disabled', !matches);
            });
        });
    }
}