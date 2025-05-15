import $ from 'jquery';
import { Controller } from '@hotwired/stimulus';

import { Modal } from 'bootstrap';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */

export default class extends Controller {
    static targets = ['modal', 'modalTitle', 'modalBody', 'modalSaveButton'];
    static values = {
        serviceUrl: String
    };
    modal = null;

    async openping(event) {
        // Params to initialize form
        let url = event.currentTarget.dataset.url;
        let message = event.currentTarget.dataset.message;
        console.log(url, message);
        let params = event.currentTarget.dataset.params ? JSON.parse(event.currentTarget.dataset.params) : [];
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal = new Modal(this.modalTarget);
        this.modal.show();
        let result = await $.ajax(url);
        console.log(result.status);

        if (result.status === 'NOTOK') {
            console.log('entra');
            this.modalBodyTarget.innerHTML = `<p class="alert alert-warning">${message}</p>`;
            $(this.modalSaveButtonTarget).hide(); 
            return;
        }


        // Loop through the result and log each value
        for (let key in result) {
            if (result.hasOwnProperty(key)) {
                console.log(`${key}: ${result[key]}`);
            }
        }

        let htmlContent = '<div>';
        for (let key in result) {
            if (result.hasOwnProperty(key)) {
                let value = result[key];
                if (key === 'datetime' && value.date) {
                    value = value.date.split(' ')[0];
                }

                htmlContent += `<p><strong>${key}:</strong> ${value}</p>`;
            }
        }
        htmlContent += '</div>';

        this.modalBodyTarget.innerHTML = htmlContent;

        $(this.modalSaveButtonTarget).show();
    }

    async openarp(event) {
        // Params to initialize form
        let url = event.currentTarget.dataset.url;
        let message = event.currentTarget.dataset.message;
        console.log(url, message);
        let params = event.currentTarget.dataset.params ? JSON.parse(event.currentTarget.dataset.params) : [];
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal = new Modal(this.modalTarget);
        this.modal.show();
        let result = await $.ajax(url);
        console.log(result.status);

        
        if (result.status === 'NOTOK') {
            console.log(message);
            this.modalBodyTarget.innerHTML = `<p class="alert alert-warning">${message}</p>`;
            $(this.modalSaveButtonTarget).hide(); 
            return;
        }

        // Loop through the result and log each value
        for (let key in result) {
            if (result.hasOwnProperty(key)) {
                console.log(`${key}: ${result[key]}`);
            }
        }

        let htmlContent = '<div>';
        for (let key in result) {
            if (result.hasOwnProperty(key)) {
                let value = result[key];
                if (key === 'datetime' && value.date) {
                    value = value.date.split(' ')[0];
                }

                htmlContent += `<p><strong>${key}:</strong> ${value}</p>`;
            }
        }
        htmlContent += '</div>';

        this.modalBodyTarget.innerHTML = htmlContent;

        $(this.modalSaveButtonTarget).show();
    }

    async opennextnumber(event) {
        $(this.modalSaveButtonTarget).hide(); 
        let url = event.currentTarget.dataset.url;
        let message = event.currentTarget.dataset.message;
        console.log(url, message);
        let params = event.currentTarget.dataset.params ? JSON.parse(event.currentTarget.dataset.params) : [];
        this.modalBodyTarget.innerHTML = 'Loading...';
        this.modal = new Modal(this.modalTarget);
        this.modal.show();
        let result = await $.ajax(url);
        console.log(result);

        
        this.modalBodyTarget.innerHTML = result;

        

    }
}
