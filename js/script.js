jQuery(function($) {
    const $toggleBg = $("." + imageSwapScriptSettings.toggleBg);
    const $toggleImg = $("." + imageSwapScriptSettings.toggleImg + " img");
    const effect = imageSwapScriptSettings.effect; // 'fade', 'zoomIn', o 'zoomOut'
    const transitionTime = imageSwapScriptSettings.transitionTime;

    function showLoading() {
        $('.loading-swap').append('<div class="loading-overlay">Loading...</div>');
    }

    function hideLoading() {
        $('.loading-overlay').remove();
    }

    function preloadImages() {
        showLoading();
        const $elements = $("." + imageSwapScriptSettings.accordionItem + ", ." + imageSwapScriptSettings.linkItem);
        let imagesLoaded = 0;
        const totalImages = $elements.length;

        $elements.each(function() {
            const id = $(this).attr('id');
            const $element = $toggleBg.length ? $toggleBg : $toggleImg;
            const isBackgroundImage = $element.is($toggleBg);
            const attribute = isBackgroundImage ? 'background-image' : 'src';
            const oldValue = isBackgroundImage ? $element.css(attribute) : $element.attr(attribute);
            const newValue = oldValue.replace(/[^\/]+(?=\.png|\.jpg|\.jpeg|\.gif|\.webp$)/gi, id);
            const imageUrl = isBackgroundImage ? extractImageUrl(newValue) : newValue;
            const image = new Image();

            image.onload = image.onerror = function() {
                imagesLoaded++;
                if (imagesLoaded === totalImages) {
                    hideLoading();
                }
            };

            image.src = imageUrl;
        });
    }

    function extractImageUrl(value) {
        const regex = /url\((['"]?)(.*)\1\)/i;
        const match = value.match(regex);
        return match ? match[2] : value;
    }

    function isMaxWidth768() {
        return window.innerWidth <= 768;
    }

    function applyEffect($element, $newElement, transitionTime, effect) {
        switch (effect) {
            case 'fade':
                $newElement.css('opacity', 0).animate({ opacity: 1 }, transitionTime, function() {
                    updateOriginalElement($element, $newElement);
                });
                break;
            case 'zoomIn':
                $newElement.css({
                    transform: 'scale(0)',
                    opacity: 0
                }).animate({ opacity: 1 }, transitionTime).css('transition', `transform ${transitionTime}ms`).css('transform', 'scale(1)');
                setTimeout(() => updateOriginalElement($element, $newElement), transitionTime);
                break;
            case 'zoomOut':
                $newElement.css({
                    transform: 'scale(1.5)',
                    opacity: 0
                }).animate({ opacity: 1 }, transitionTime).css('transition', `transform ${transitionTime}ms`).css('transform', 'scale(1)');
                setTimeout(() => updateOriginalElement($element, $newElement), transitionTime);
                break;
        }
    }

    function updateOriginalElement($element, $newElement) {
        if ($element.is($toggleBg)) {
            $element.css('background-image', $newElement.css('background-image'));
        } else {
            $element.attr('src', $newElement.attr('src'));
            if ($newElement.attr('srcset')) {
                $element.attr('srcset', $newElement.attr('srcset'));
            }
        }
        $newElement.remove();
    }

    function updateImage(id) {
        const $element = $toggleBg.length ? $toggleBg : $toggleImg;
        const isBackgroundImage = $element.is($toggleBg);
        const $container = $element.parent();
		
		const transitionSpeeds = {
        fast: 200,
        slow: 600
      };
		
    // Acceder al tiempo de transición usando el valor de transitionTime ('fast' o 'slow')
    const transitionTime = transitionSpeeds[imageSwapScriptSettings.transitionTime] || 200; 

        $container.css({
            position: 'relative',
            overflow: 'hidden'
        });

        let $newElement;

        if (isBackgroundImage) {
            const oldUrl = $element.css('background-image').slice(4, -1).replace(/["']/g, "");
            const newUrl = oldUrl.replace(/[^\/]+(?=\.\w+$)/, id);

            $newElement = $('<div>').css({
                position: 'absolute',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                backgroundImage: `url(${newUrl})`,
                backgroundSize: 'cover',
               	backgroundPosition: isMaxWidth768() ? 'left' : 'center', 
				boxSizing: 'border-box'
            });
        } else {
            const oldSrc = $element.attr('src');
            const newSrc = oldSrc.replace(/[^\/]+(?=\.png|\.jpg|\.jpeg|\.gif|\.webp$)/gi, id);

            $newElement = $('<img>').attr('src', newSrc).css({
                position: 'absolute',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                objectFit: 'cover'
            });

            const srcset = $element.attr('srcset');
            if (srcset) {
                const newSrcset = srcset.replace(/[^\/]+(?=\.png|\.jpg|\.jpeg|\.gif|\.webp$)/gi, id);
                $newElement.attr('srcset', newSrcset);
            }
        }

        $container.append($newElement);
        applyEffect($element, $newElement, transitionTime, effect);
    }

    $("." + imageSwapScriptSettings.accordionItem + ", ." + imageSwapScriptSettings.linkItem).click(function(e) {
        let target = $(e.target);
        let isLinkOrInsideLink = target.is("a[href]") || target.closest("a[href]").length > 0;

        if (!isLinkOrInsideLink) {
            e.stopPropagation();
            e.preventDefault();
            const id = $(this).attr('id');
            updateImage(id);
        }
    });

    preloadImages();
});
