import { Controller } from 'stimulus';

export default class extends Controller {
    imagePreview() {
        const $file = this.element.getElementsByClassName('input-image')[0],
            reader = new FileReader();

        reader.readAsDataURL($file.files[0]);
        reader.onload = (e)=> {
            document.querySelector('.image-preview').src = e.target.result;
        };
    }

}
