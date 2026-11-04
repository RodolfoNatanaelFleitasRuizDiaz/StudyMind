<?php
// Helpbit, la mascota de StudyMind.
// Imágenes esperadas en /img: helpbit-feliz.png, helpbit-pensando.png,
// helpbit-estudiando.png, helpbit-descansando.png
// Si falta una imagen, se muestra un osito 🧸 de respaldo.
function helpbit(string $pose = 'feliz', string $prefix = '../', string $size = ''): string {
    $poses = ['feliz', 'pensando', 'estudiando', 'descansando'];
    if (!in_array($pose, $poses, true)) $pose = 'feliz';
    $src = htmlspecialchars($prefix) . 'img/helpbit-' . $pose . '.png';
    $cls = 'helpbit-img' . ($size ? ' ' . $size : '');
    return '<img class="' . $cls . '" src="' . $src . '" alt="Helpbit" '
         . 'onerror="this.style.display=\'none\';this.nextElementSibling.style.display=\'inline-block\'">'
         . '<span class="helpbit-emoji" style="display:none" aria-hidden="true">🧸</span>';
}
