/**
 * XSTO Pinned Scroll — Front-end scroll pinning and active item logic
 */
(function () {
    'use strict';

    var spacer, section, inner, rightCol, track, items, mainImages, stack1Images, stack2Images, cardLabel;
    var count = 0;
    var activeIdx = 0;
    var ticking = false;

    function isMobile() {
        return window.innerWidth <= 768;
    }

    function init() {
        spacer = document.querySelector('.xsto-ps-spacer');
        if (!spacer) return;

        section     = spacer.querySelector('.xsto-ps');
        inner       = spacer.querySelector('.xsto-ps__inner');
        rightCol    = spacer.querySelector('.xsto-ps__right');
        track       = spacer.querySelector('.xsto-ps__right-track');
        items       = spacer.querySelectorAll('.xsto-ps__item');
        mainImages  = spacer.querySelectorAll('.xsto-ps__card-image');
        stack1Images = spacer.querySelectorAll('.xsto-ps__card-stack--1 .xsto-ps__stack-image');
        stack2Images = spacer.querySelectorAll('.xsto-ps__card-stack--2 .xsto-ps__stack-image');
        cardLabel   = spacer.querySelector('.xsto-ps__card-label');
        count       = items.length;

        if (count === 0) return;

        setSpacer();
        setActive(0);

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onResize, { passive: true });
    }

    function setSpacer() {
        if (isMobile()) {
            spacer.style.height = '';
            return;
        }
        // Each category gets 100vh of scroll distance
        spacer.style.height = (count * 100) + 'vh';
    }

    function onResize() {
        setSpacer();
        if (!ticking) {
            requestAnimationFrame(update);
            ticking = true;
        }
    }

    function onScroll() {
        if (!ticking) {
            requestAnimationFrame(update);
            ticking = true;
        }
    }

    function update() {
        ticking = false;

        if (isMobile()) return;

        var rect = spacer.getBoundingClientRect();
        var spacerH = spacer.offsetHeight;
        var scrollRange = spacerH - window.innerHeight;

        if (scrollRange <= 0) return;

        // Progress: 0 (top of spacer at viewport top) to 1 (bottom of spacer at viewport bottom)
        var progress = Math.min(Math.max(-rect.top / scrollRange, 0), 1);

        // Map progress to active index
        var newIdx = Math.min(Math.floor(progress * count), count - 1);

        if (newIdx !== activeIdx) {
            setActive(newIdx);
        }

        // Translate the right-column track so the active item is visible
        translateTrack(newIdx, progress);
    }

    function setActive(idx) {
        // Items
        items[activeIdx].classList.remove('is-active');
        items[idx].classList.add('is-active');

        // Main card images — show the active category image
        mainImages[activeIdx].classList.remove('is-active');
        mainImages[idx].classList.add('is-active');

        // Stack 1 (middle) — show the NEXT category image (or wrap)
        var stack1Idx = (idx + 1) % count;
        var prevStack1 = (activeIdx + 1) % count;
        if (stack1Images.length > 0) {
            stack1Images[prevStack1].classList.remove('is-active');
            stack1Images[stack1Idx].classList.add('is-active');
        }

        // Stack 2 (back) — show the category after next (or wrap)
        var stack2Idx = (idx + 2) % count;
        var prevStack2 = (activeIdx + 2) % count;
        if (stack2Images.length > 0) {
            stack2Images[prevStack2].classList.remove('is-active');
            stack2Images[stack2Idx].classList.add('is-active');
        }

        // Update card label text
        if (cardLabel) {
            cardLabel.textContent = items[idx].getAttribute('data-label') || '';
        }

        activeIdx = idx;
    }

    function translateTrack(idx, progress) {
        if (!track || !rightCol) return;

        // Calculate how much to shift the track upward
        // Each item occupies an equal portion of the scroll
        var itemFraction = 1 / count;
        var itemProgress = (progress - (idx * itemFraction)) / itemFraction;
        itemProgress = Math.min(Math.max(itemProgress, 0), 1);

        // Get the target item's offset within the track
        var targetItem = items[idx];
        if (!targetItem) return;

        var trackRect = track.getBoundingClientRect();
        var itemRect  = targetItem.getBoundingClientRect();
        var rightRect = rightCol.getBoundingClientRect();

        // We want the active item vertically centered in the right column
        var rightCenter = rightRect.height / 2;
        var itemCenter  = targetItem.offsetTop + (targetItem.offsetHeight / 2);
        var translateY  = -(itemCenter - rightCenter);

        // Clamp so we don't over-translate
        var maxTranslate = -(track.scrollHeight - rightCol.offsetHeight);
        translateY = Math.max(translateY, maxTranslate);
        translateY = Math.min(translateY, 0);

        track.style.transform = 'translateY(' + translateY + 'px)';
    }

    // Init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
