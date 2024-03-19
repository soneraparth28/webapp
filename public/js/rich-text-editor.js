const optionButtons = document.querySelectorAll("#rte-options button:not(.opt-advanced)");
const advancedOptionButtons = document.querySelectorAll("#rte-options .opt-advanced");
const fontName = document.getElementById("rte-options-font-name");
const fontSize = document.getElementById("rte-options-font-size");
const writingArea = document.getElementById("text-input");
const linkButton = document.getElementById("rte-options-link");
const alignButtons = document.querySelectorAll("#rte-options .opt-align");
const spacingButtons = document.querySelectorAll("#rte-options .opt-spacing");
const formatButtons = document.querySelectorAll("#rte-options .opt-format");
const scriptButtons = document.querySelectorAll("#rte-options .opt-script");
const fontList = ['Arial', 'Verdana', 'Times New Roman', 'Garamond', 'Georgia', 'Courier New', 'cursive', 'Sans-Serif'];



const richTextInit = () => {
    highlighter(alignButtons, true);
    highlighter(spacingButtons, true);
    highlighter(formatButtons, false);
    highlighter(scriptButtons, true);


    fontList.map(value => {
        let option = document.createElement("option");
        option.value = value;
        option.innerHTML = value;
        fontName.appendChild(option);
    })

    for(let i = 1; i < 7; i++) {
        let option = document.createElement("option");
        option.value = i;
        option.innerHTML = i;
        fontSize.appendChild(option);
    }
    fontSize.value = 3;


    optionButtons.forEach(button => {
        button.addEventListener("click", () => {
            modifyText(button.getAttribute("data-command"), false, null)
        })
    })

    advancedOptionButtons.forEach(button => {
        button.addEventListener("change", () => {
            modifyText(button.getAttribute("data-command"), false, button.value)
        })
    })

    linkButton.addEventListener("click", () => {
        let userLink = prompt("Enter a URL");
        if(!(/http/i.test(userLink))) userLink = "https://" + userLink;
        modifyText(linkButton.getAttribute("data-command"), false, userLink)
    })
}


const modifyText = (command, defaultUi, value) => {
    document.execCommand(command, defaultUi, value);
}



const highlighter = (elements, needsHighlightRemoval) => {
    elements.forEach(button => {
        button.addEventListener("click", () => {
            if(needsHighlightRemoval) {
                let alreadyActive = false;
                if(button.classList.contains("active")) alreadyActive = true;

                highlighterRemover(elements);
                if(!alreadyActive) button.classList.add("active");

            }
            else button.classList.toggle("active");
        })
    })
}

const highlighterRemover = (elements) => {
    elements.forEach(button => {
        button.classList.remove("active");
    })
}





const docReady = (fn) => {
    // see if DOM is already available
    if (document.readyState === "complete" || document.readyState === "interactive") {
        // call on next available tick
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}
docReady(function () { richTextInit(); })
