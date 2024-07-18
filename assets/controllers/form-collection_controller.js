import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        //on crée un bouton
        const btn = document.createElement('button')
        btn.setAttribute('class', 'btn btn-secondary')
        btn.innerText = 'Ajouter un élément'
        btn.setAttribute('type', 'button')
        btn.addEventListener('click', this.addElement)
        //on rajoute le bouton à la fin de la liste(?)
        this.addElement.append(btn)
    }

    //méthode appelée au clic sur le bouton
    /**
     * 
     * @param {MouseEvent} e 
     */
    addElement = (e) => {
        e.preventDefault()
    }
}
