import { Controller } from 'stimulus';

export default class extends Controller {
    imagePreview(clickedElementEvent) {
        const $file = clickedElementEvent.currentTarget,
            Reader = new FileReader();

        Reader.readAsDataURL($file.files[0]);
        Reader.onload = (parsedElementEvent)=> {
            const $parent = $file.parentNode,
                id = $file.getAttribute('id'),
                $imagePreview = document.querySelector('#preview-'+id);

            if (null !== $imagePreview) {
                $imagePreview.remove();
            }

            $parent.insertAdjacentHTML('afterend', '<img id="preview-' + id + '" src="' + parsedElementEvent.currentTarget.result + '" class="image-preview" alt="Preview">');
        };
    }

}
