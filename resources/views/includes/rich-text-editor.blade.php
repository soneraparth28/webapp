



<div id="rich-text-editor">
    <div id="rte-options">
        <button id="rte-options-bold" class="opt-format" data-command="bold">
            <i class="fa fa-bold"></i>
        </button>
        <button id="rte-options-italic" class="opt-format" data-command="italic">
            <i class="fa fa-italic"></i>
        </button>
        <button id="rte-options-underline" class="opt-format" data-command="underline">
            <i class="fa fa-underline"></i>
        </button>
        <button id="rte-options-strikethrough" class="opt-format" data-command="strikethrough">
            <i class="fa fa-strikethrough"></i>
        </button>
        <button id="rte-options-superscript" class="opt-script" data-command="superscript">
            <i class="fa fa-superscript"></i>
        </button>
        <button id="rte-options-subscript" class="opt-script" data-command="subscript">
            <i class="fa fa-subscript"></i>
        </button>
        <button id="rte-options-ordered-list" data-command="insertOrderedList">
            <i class="fa fa-list-ol"></i>
        </button>
        <button id="rte-options-unordered-list" data-command="insertUnorderedList">
            <i class="fa fa-list-ul"></i>
        </button>
        <button id="rte-options-undo" data-command="undo">
            <i class="fa fa-rotate-left"></i>
        </button>
        <button id="rte-options-redo" data-command="redo">
            <i class="fa fa-rotate-right"></i>
        </button>
        <button id="rte-options-link" data-command="createLink" class="opt-advanced">
            <i class="fa fa-link"></i>
        </button>
        <button id="rte-options-unlink" data-command="unlink">
            <i class="fa fa-unlink"></i>
        </button>
        <button id="rte-options-justify-left" class="opt-align" data-command="justifyLeft">
            <i class="fa fa-align-left"></i>
        </button>
        <button id="rte-options-justify-center" class="opt-align" data-command="justifyCenter">
            <i class="fa fa-align-center"></i>
        </button>
        <button id="rte-options-justify-right" class="opt-align" data-command="justifyRight">
            <i class="fa fa-align-right"></i>
        </button>
        <button id="rte-options-justify-full" class="opt-align" data-command="justifyFull">
            <i class="fa fa-align-justify"></i>
        </button>
        <button id="rte-options-indent" class="opt-spacing" data-command="indent">
            <i class="fa fa-indent"></i>
        </button>
        <button id="rte-options-outdent" class="opt-spacing" data-command="outdent">
            <i class="fa fa-outdent"></i>
        </button>
        <select id="rte-options-headings" class="opt-advanced" data-command="formatBlock">
            <option value="H1">H-1</option>
            <option value="H2">H-2</option>
            <option value="H3">H-3</option>
            <option value="H4">H-4</option>
            <option value="H5">H-5</option>
            <option value="H6">H-6</option>
        </select>
        <select id="rte-options-font-name" class="opt-advanced" data-command="fontName"></select>
        <select id="rte-options-font-size" class="opt-advanced" data-command="fontSize"></select>
        <div class="input-wrapper">
            <input type="color" id="rte-options-font-color" class="opt-advanced" data-command="foreColor"/>
            <label for="rte-options-font-color">Font color</label>
        </div>
        <div class="input-wrapper">
            <input type="color" id="rte-options-highlight-color" class="opt-advanced"  data-command="backColor"/>
            <label for="rte-options-highlight-color">Highlight color</label>
        </div>
    </div>
    <div id="text-input" contenteditable="true"></div>

</div>
<script src="{{asset("public/js/rich-text-editor.js")}}"></script>
