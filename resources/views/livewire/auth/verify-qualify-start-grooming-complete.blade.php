<div class="vq-start-grooming-complete" wire:key="verify-qualify-start-grooming-complete">
    <svg xmlns="http://www.w3.org/2000/svg" width="151" height="100" viewBox="0 0 151 100" fill="none">
        <path
            d="M23.7623 17.8218C23.7623 7.97908 31.7414 0 41.5841 0H105.941C115.783 0 123.762 7.97908 123.762 17.8218C123.762 27.6645 115.783 35.6436 105.941 35.6436H41.5841C31.7414 35.6436 23.7623 27.6645 23.7623 17.8218Z"
            fill="#FFE0B2" />
        <path
            d="M0 51.981C0 42.4117 7.75744 34.6543 17.3267 34.6543H82.6733C92.2425 34.6543 100 42.4117 100 51.981C100 61.5503 92.2426 69.3078 82.6733 69.3078H17.3267C7.75746 69.3078 0 61.5503 0 51.981Z"
            fill="#FBAC83" fill-opacity="0.62" />
        <path
            d="M50.4949 82.1773C50.4949 72.3346 58.474 64.3555 68.3167 64.3555H132.673C142.516 64.3555 150.495 72.3346 150.495 82.1773C150.495 92.0199 142.516 99.999 132.673 99.999H68.3167C58.474 99.999 50.4949 92.0199 50.4949 82.1773Z"
            fill="#D8E8B7" fill-opacity="0.78" />
        <path
            d="M52.4084 42.0073L47.0184 54.1367C40.2131 69.4413 36.8141 77.0937 40.5284 80.808C44.2428 84.5223 51.8877 81.1197 67.1924 74.318L79.3291 68.9207C88.5581 64.8177 93.1744 62.768 93.9077 58.8117C94.6411 54.8553 91.0697 51.284 83.9271 44.145L77.1878 37.402C70.0488 30.2593 66.4774 26.688 62.5211 27.425C58.5648 28.1583 56.5151 32.7747 52.4121 42.0037M55.4957 40.1667L81.1624 65.8333M48.1624 58.5L62.8291 73.1667M90.3291 31L101.329 20M83.7181 9C85.1847 11.4457 86.3544 17.8 79.3291 23.6667M112.329 37.611C109.883 36.1443 103.529 34.9747 97.6624 42M97.6624 9V9.07333M112.329 23.6667V23.74M108.662 49.3333V49.4067M71.9957 12.6667V12.74"
            stroke="#3B3731" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
    </svg>

    <h1 class="vq-sge-title">Your account is all set!</h1>
    <p class="vq-sge-lead">We’ve saved your details and you’re ready to start grooming & earning!</p>
    <p class="vq-sge-muted">You can update your details at any time from your account.</p>

    <button type="button" wire:click="goToDashboard" wire:loading.attr="disabled" wire:target="goToDashboard"
        class="vq-sge-cta">My Business Profile</button>
</div>

<style>
    .vq-start-grooming-complete {
        max-width: 36rem;
        margin: 0 auto;
        padding: 2rem 1.5rem 3rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .vq-start-grooming-complete>svg {
        margin-bottom: 2.5rem;
    }

    .vq-sge-title {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 36px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
        margin-bottom: 1rem;

    }

    .vq-sge-lead {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .vq-sge-muted {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .vq-sge-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 179px;
        height: 48px;
        border: none;
        border-radius: 96px;
        background: #FFC97A;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-top: 2rem;
        cursor: pointer;
    }
</style>
