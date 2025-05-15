import $ from 'jquery';
import { Controller } from '@hotwired/stimulus';
import { Popover } from 'bootstrap';

export default class extends Controller {
    static targets = [];
    static values = {
        serviceUrl: String
    };

    currentTargetObject = null;
    popover = null;

    async toggle(event) {
        this.currentTargetObject = event.currentTarget;
        let result = await $.ajax(this.serviceUrlValue);
        this.popover = Popover.getOrCreateInstance(this.currentTargetObject, { 
            "content" : result.name,
            "placement" : "top",
            "trigger": 'hover',
        });
        this.popover.toggle();
    }
}
