function previewImageUpload() {
    const $fileUpload = document.querySelector('input[type=file]');
    if (null === $fileUpload) {
        return false;
    }

    $fileUpload.addEventListener('change', ()=> {
        const reader = new FileReader();
        reader.readAsDataURL($fileUpload.files[0]);
        reader.onload = (e)=> {
            document.querySelector('.img-preview').src = e.target.result;
        };
    });
}
/*
function setPostTitleLabel() {
    document.querySelector('#post_type').addEventListener('change', (event)=> {
        const $title = document.querySelector('#post_title');
        $title.placeholder = event.target.selectedOptions[0].text + '...';
        $title.focus();
    });
}
*/
(function () {
    previewImageUpload();
    //setPostTitleLabel();
})();








