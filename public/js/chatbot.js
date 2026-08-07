function initFursgoChatbot() {
    const panel = document.getElementById('chat-panel');
    const chatBtn = document.getElementById('chat-btn');
    const bubbleWrapper = document.querySelector('.chat-bubble-wrapper');
    const openIcon = document.getElementById('chat-open-icon');
    const closeIcon = document.getElementById('chat-close-icon');

    if (!panel || !chatBtn || chatBtn.dataset.chatbotReady === '1') {
        return;
    }

    chatBtn.dataset.chatbotReady = '1';

    function openChat(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        panel.classList.add('open');
        document.body.classList.add('chat-open');

        if (openIcon) openIcon.style.display = 'none';
        if (closeIcon) closeIcon.style.display = 'block';
    }

    function closeChat() {
        panel.classList.remove('open');
        document.body.classList.remove('chat-open');

        if (openIcon) openIcon.style.display = 'block';
        if (closeIcon) closeIcon.style.display = 'none';
    }

    function toggleChat(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        if (panel.classList.contains('open')) {
            closeChat();
        } else {
            openChat();
        }
    }

    chatBtn.addEventListener('click', toggleChat);

    document.querySelectorAll('[data-open-chat]').forEach(function(link) {
        link.addEventListener('click', openChat);
    });

    document.addEventListener('click', function(e) {
        const clickedBubble = bubbleWrapper && bubbleWrapper.contains(e.target);

        if (!panel.contains(e.target) && !clickedBubble) {
            closeChat();
        }
    });

    initChatbotCards();
    initChatbotRating();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFursgoChatbot);
} else {
    initFursgoChatbot();
}

document.addEventListener('livewire:navigated', initFursgoChatbot);

function initChatbotCards() {
    const card1 = document.querySelector('.fs-card-1');
    if (!card1) return;

    const cards = {
        1: card1,
        2: document.querySelector('.fs-card-2'),
        3: document.querySelector('.fs-card-3'),
        4: document.querySelector('.fs-card-4'),
        5: document.querySelector('.fs-card-5'),
        6: document.querySelector('.fs-card-6')
    };

    const bookingBtn = document.querySelector('.fs-card-1 .fs-menu-item:first-child');
    const optionButtons = document.querySelectorAll('.fs-card-2 .fs-opt-btn');
    const submitRequestBtn = document.querySelector('.fs-submit-request');
    const doneBtn = document.querySelector('.iamdone-btn');
    const endConversationBtn = document.querySelector('.end-conversation');
    const backBtns = document.querySelectorAll('.fs-back-arrow');

    let currentStep = 1;

    function showCard(step) {
        currentStep = step;

        Object.values(cards).forEach(function(card) {
            if (card) card.style.display = 'none';
        });

        if (cards[step]) {
            cards[step].style.display = 'block';
        }

        document.querySelectorAll('.fs-card-body').forEach(function(chatBody) {
            chatBody.scrollTop = chatBody.scrollHeight;
        });
    }

    showCard(1);

    if (bookingBtn) {
        bookingBtn.addEventListener('click', function() {
            showCard(2);
        });
    }

    optionButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            showCard(3);
        });
    });

    if (submitRequestBtn) {
        submitRequestBtn.addEventListener('click', function() {
            showCard(4);
        });
    }

    if (doneBtn) {
        doneBtn.addEventListener('click', function() {
            showCard(5);
        });
    }

    if (endConversationBtn) {
        endConversationBtn.addEventListener('click', function() {
            showCard(6);
        });
    }

    backBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (currentStep > 1) {
                showCard(currentStep - 1);
            }
        });
    });
}

function initChatbotRating() {
    const stars = document.querySelectorAll('.fs-star');
    const ratingInput = document.getElementById('ratingValue');

    if (!stars.length || !ratingInput) return;

    let selectedRating = 0;

    function updateStars(rating) {
        stars.forEach(function(star) {
            const value = parseInt(star.dataset.value, 10);

            if (value <= rating) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }

    stars.forEach(function(star) {
        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.value, 10);
            ratingInput.value = selectedRating;
            updateStars(selectedRating);
        });

        star.addEventListener('mouseover', function() {
            updateStars(parseInt(this.dataset.value, 10));
        });

        star.addEventListener('mouseout', function() {
            updateStars(selectedRating);
        });
    });
}
