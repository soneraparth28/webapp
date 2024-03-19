function isFloat(n){
    return Number(n) === n && n % 1 !== 0;
}

const progressLoader = {
    set: (parent, size = "lg") => {
        let element = document.createElement("div");
        element.className = "circular-progress " + size;
        let progressValueElement = document.createElement("span");
        progressValueElement.className = "color-dark progress-value";
        progressValueElement.textContent = "0%";

        element.append(progressValueElement)
        parent.append(element)
        return element;
    },
    update: (progressElement, value) => {
        if(typeof value !== "number" || isFloat(value)) value = parseInt(value)
        if(value > 100) value = 100;
        let rotationValue = Math.ceil(3.6 * value);

        let progressTextElement = progressElement.querySelector(".progress-value");

        progressElement.style.background = `conic-gradient(var(--dark) ${rotationValue}deg, var(--light-gray) 0deg)`;
        progressTextElement.textContent = `${value}%`;
        return progressElement;
    },
    delete: (progressElement) => {
        progressElement.remove();
        return null;
    }
}



