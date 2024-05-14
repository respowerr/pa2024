document.addEventListener('DOMContentLoaded', function() {
    const langSwitcher = document.getElementById('lang_switch');

    function loadLanguage(lang) {
        fetch(`/languages/${lang}.json`)
            .then(response => response.json())
            .then(translations => {
                document.querySelectorAll('[data-translate]').forEach(element => {
                    const key = element.getAttribute('data-translate');
                    element.textContent = translations[key] || "Key not found: " + key;
                });
            })
            .catch(error => console.error('Error loading the translation file:', error));
    }

    langSwitcher.addEventListener('change', function() {
        loadLanguage(this.value);
    });

    loadLanguage('fr');
});
