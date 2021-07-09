let flashes = document.querySelectorAll('.flash');

flashes.forEach(function (value) {
    setTimeout(function () {value.classList.add('hide-flash');}, 5000);
    setTimeout(function () {value.classList.add('d-none');}, 5800);
})
