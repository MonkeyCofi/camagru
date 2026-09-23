<?php

function home() {
    return <<< "EOT"
    <div style="display: flex; justify-content: center; align-items: center;">
        <canvas id="canvas" width="200" height="200"></canvas>
    </div>
    <div class="modal-background"></div>
    <div class="modal">
        <div class='modal-text'>
            <p>This is a modal</p>
            </div>
        <div class='modal-buttons'>
            <button>Yes</button>
            <button>No</button>
        </div>
    </div>
    <script src="canvas.js"></script>
    EOT;
}

