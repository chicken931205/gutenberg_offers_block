
let getSiblings = function (e) {
    let siblings = []; 
    if(!e.parentNode) {
        return siblings;
    }

    let sibling  = e.parentNode.firstChild;
    while (sibling) {
        if (sibling.nodeType === 1) {
            siblings.push(sibling);
        }
        sibling = sibling.nextSibling;
    }

    return siblings;
};

function handleDotClick( event, dot_index, offer_index ) {
    const carousel_scrollWidth = document.querySelector('.carousel_' + offer_index).scrollWidth;//include overflow
    const carousel_offsetWidth = document.querySelector('.carousel_' + offer_index).offsetWidth;//not include overflow

    const slides = document.querySelectorAll('.carousel_' + offer_index + ' img');
    const keyframes = [
        { translateX: 0 },
        { translateX: -1 * (carousel_scrollWidth - carousel_offsetWidth) / 2 },
        { translateX: -1 * (carousel_scrollWidth - carousel_offsetWidth) }
    ];
    slides.forEach((slide, i) => {
        anime({
            targets: slide,
            translateX: keyframes[dot_index].translateX,
            duration: 500,
            easing: 'linear'
          });
    });

    let siblings = getSiblings( event.target );

    siblings.forEach((e, i) => {
        if (i === (dot_index)) {
            e.classList.add('active');
        } else {
            e.classList.remove('active');
        }
    });
}
