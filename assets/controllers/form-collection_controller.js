import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        //on calcule l'index de l'élément = nb d'éléments enfants
        this.index = this.addElement.childElementCount
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
        //éviter le comportement par défaut
        e.preventDefault()

        //createRange : créer un élément html à partir d'une chaine de caractères
        const element = document.createRange().createContextualFragment(
            //1e paramètre = la string, que je crée à partir de mon élément et en intégrant l'index
            this.element.dataset['prototype'].replaceAll('__name__', this.index)
        ).firstElementChild //il n'y a qu'un élément(?); je le récupère pour avoir un élément et non un fragment(?)

        //on incrément l'index puisqu'on a un élément en plus
        this.index++

        //prendre la cible courante et insérer notre élément juste avant le bouton
        e.currentTarget.insertAdjacentElement('beforebegin', element)
    }
}
