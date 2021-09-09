import { Controller } from 'stimulus';

export default class extends Controller {
    connect() {
        this.$type = document.querySelector('#post_type');
        this.$title = document.querySelector('#post_title');
        this.$description = document.querySelector('#post_description');
        this.$isQuestion = document.querySelector('#post_isQuestion');
    }

    typeChange(event) {
        const $titleLabel = this.$title.previousSibling;
        const currentType = 0 !== +this.$type.selectedIndex
            ? ' : ' + event.target.selectedOptions[0].text
            : '';

        this.initialLabel = this.initialLabel || $titleLabel.innerHTML;
        $titleLabel.innerHTML = this.initialLabel + currentType;
        this.$title.focus();

        this.applyWithType();
    }

    validate(event) {
        if (1 === +this.$type.selectedOptions[0].value && this.$title.value.indexOf(' ') > 0) {
            event.preventDefault();
            alert('Erreur ! Avec le type [MOT] les espaces ne sont pas autorisés.');
            return false;
        }
    }

    applyWithType() {
        const typeValue = +this.$type.selectedOptions[0].value,
            $descriptionParent = this.$description.parentNode,
            $isQuestionParent = this.$isQuestion.parentNode;

        switch (typeValue) {
            case 1:
            case 2:
            case 3:
                this.$description.required = true;
                this.$description.disabled = false;
                $descriptionParent.classList.remove('d-none');
                $isQuestionParent.classList.remove('d-none');
                break;

            case 4:
                this.$description.required = false;
                this.$description.disabled = true;
                $descriptionParent.classList.add('d-none');
                $isQuestionParent.classList.add('d-none');
                break;
            default:
                break;
        }
    }
}
