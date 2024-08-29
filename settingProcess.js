document.getElementById('themeForm').addEventListener('submit', function() {
    const selectedTheme = document.getElementById('themeSelect').value;
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "update_theme.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            location.reload();
        }
    };
    xhr.send("theme=" + encodeURIComponent(selectedTheme));
});

function chooseColorTheme() {
    document.getElementById('colorThemeModal').style.display='block';
}

function closeColorThemeModal() {
    document.getElementById('colorThemeModal').style.display = 'none';
}

