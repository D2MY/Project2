let flashes = document.querySelectorAll('.flash');

flashes.forEach(function (value) {
    setTimeout(function () {value.classList.add('hide-flash');}, 3000);
    setTimeout(function () {value.classList.add('d-none');}, 3800);
})
