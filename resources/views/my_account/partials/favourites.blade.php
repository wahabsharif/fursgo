<?php

/**
 * Favourites tab — Groomers + Space Hosts
 * Card patterns adapted from my_profile_old.php, styled for the new account page.
 */
$favTrash = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none">
  <path d="M2.24229 14C1.86115 14 1.53515 13.8594 1.26429 13.5783C0.993433 13.2972 0.857719 12.9601 0.857148 12.5672V1.57134H0.428577C0.306863 1.57134 0.205149 1.52873 0.123434 1.4435C0.0417202 1.35828 0.000577299 1.25263 5.87083e-06 1.12657C-0.000565557 1.00051 0.0405773 0.89516 0.123434 0.810526C0.206291 0.725893 0.308006 0.683576 0.428577 0.683576H3.42857C3.42857 0.500106 3.49429 0.340309 3.62572 0.204185C3.75714 0.0680617 3.91143 0 4.08857 0H7.91143C8.08857 0 8.24286 0.0680617 8.37428 0.204185C8.50571 0.340309 8.57143 0.500106 8.57143 0.683576H11.5714C11.6931 0.683576 11.7949 0.726189 11.8766 0.811414C11.9583 0.896639 11.9994 1.00228 12 1.12834C12.0006 1.25441 11.9594 1.35975 11.8766 1.44439C11.7937 1.52902 11.692 1.57134 11.5714 1.57134H11.1429V12.5663C11.1429 12.9604 11.0071 13.2978 10.7357 13.5783C10.4643 13.8588 10.1386 13.9994 9.75857 14H2.24229ZM10.2857 1.57134H1.71429V12.5663C1.71429 12.7255 1.76372 12.8563 1.86258 12.9587C1.96143 13.061 2.088 13.1122 2.24229 13.1122H9.75857C9.91228 13.1122 10.0386 13.061 10.1374 12.9587C10.2363 12.8563 10.2857 12.7255 10.2857 12.5663V1.57134ZM4.54972 11.3367C4.67143 11.3367 4.77343 11.2941 4.85572 11.2089C4.938 11.1237 4.97886 11.0183 4.97829 10.8928V3.79074C4.97829 3.66468 4.93714 3.55933 4.85486 3.4747C4.77257 3.39007 4.67057 3.34745 4.54886 3.34686C4.42715 3.34627 4.32543 3.38888 4.24372 3.4747C4.162 3.56052 4.12114 3.66586 4.12114 3.79074V10.8928C4.12114 11.0189 4.16229 11.1242 4.24457 11.2089C4.32686 11.2941 4.42857 11.3367 4.54972 11.3367ZM7.45114 11.3367C7.57286 11.3367 7.67457 11.2941 7.75628 11.2089C7.838 11.1237 7.87886 11.0183 7.87886 10.8928V3.79074C7.87886 3.66468 7.83771 3.55933 7.75543 3.4747C7.67314 3.38947 7.57143 3.34686 7.45028 3.34686C7.32857 3.34686 7.22657 3.38947 7.14429 3.4747C7.062 3.55992 7.02114 3.66527 7.02171 3.79074V10.8928C7.02171 11.0189 7.06286 11.1242 7.14514 11.2089C7.22743 11.2935 7.32943 11.3361 7.45114 11.3367Z" fill="#3B3731"/>
</svg>';

$favShieldGroomer = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" viewBox="0 0 18 20" fill="none" aria-hidden="true"><ellipse cx="9.36358" cy="9.74945" rx="5.52471" ry="5.19965" fill="white"/><path d="M9.10904 0.109186C8.95806 0.0376504 8.7963 0 8.62734 0C8.45839 0 8.29663 0.0376504 8.14565 0.109186L1.37679 3.11745C0.585956 3.4676 -0.00357837 4.28462 1.63506e-05 5.27106C0.01799 9.00598 1.48464 15.8395 7.67834 18.9457C8.27866 19.2469 8.97603 19.2469 9.57635 18.9457C15.7701 15.8395 17.2367 9.00598 17.2547 5.27106C17.2583 4.28462 16.6687 3.4676 15.8779 3.11745L9.10904 0.109186ZM5.20877 10.7755C5.38131 10.8207 5.56464 10.8433 5.75157 10.8433C7.0205 10.8433 8.05219 9.76275 8.05219 8.43369V6.02407H9.64106C10.076 6.02407 10.475 6.28009 10.6691 6.69048L10.928 7.22888H13.2286C13.5449 7.22888 13.8037 7.49996 13.8037 7.83128V9.0361C13.8037 10.7002 12.5168 12.0481 10.928 12.0481H9.2025V13.957C9.2025 14.2319 8.99041 14.4578 8.7244 14.4578C8.6597 14.4578 8.59499 14.4427 8.53748 14.4163L4.98949 12.8237C4.75224 12.7183 4.60126 12.4736 4.60126 12.2063C4.60126 12.1008 4.62283 11.9992 4.66956 11.9051L5.20877 10.7755ZM5.17641 6.02407H6.90188V8.43369C6.90188 9.1001 6.38783 9.6385 5.75157 9.6385C5.1153 9.6385 4.60126 9.1001 4.60126 8.43369V6.62647C4.60126 6.29515 4.86008 6.02407 5.17641 6.02407ZM9.77765 7.83128C9.77765 7.67152 9.71706 7.51829 9.6092 7.40532C9.50133 7.29235 9.35504 7.22888 9.2025 7.22888C9.04996 7.22888 8.90367 7.29235 8.7958 7.40532C8.68794 7.51829 8.62734 7.67152 8.62734 7.83128C8.62734 7.99105 8.68794 8.14428 8.7958 8.25725C8.90367 8.37022 9.04996 8.43369 9.2025 8.43369C9.35504 8.43369 9.50133 8.37022 9.6092 8.25725C9.71706 8.14428 9.77765 7.99105 9.77765 7.83128Z" fill="#C9DDA0"/></svg>';

$favShieldSpace = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="20" viewBox="0 0 18 20" fill="none" aria-hidden="true"><ellipse cx="9.36358" cy="9.74945" rx="5.52471" ry="5.19965" fill="white"/><path d="M9.10904 0.109186C8.95806 0.0376504 8.7963 0 8.62734 0C8.45839 0 8.29663 0.0376504 8.14565 0.109186L1.37679 3.11745C0.585956 3.4676 -0.00357837 4.28462 1.63506e-05 5.27106C0.01799 9.00598 1.48464 15.8395 7.67834 18.9457C8.27866 19.2469 8.97603 19.2469 9.57635 18.9457C15.7701 15.8395 17.2367 9.00598 17.2547 5.27106C17.2583 4.28462 16.6687 3.4676 15.8779 3.11745L9.10904 0.109186ZM5.20877 10.7755C5.38131 10.8207 5.56464 10.8433 5.75157 10.8433C7.0205 10.8433 8.05219 9.76275 8.05219 8.43369V6.02407H9.64106C10.076 6.02407 10.475 6.28009 10.6691 6.69048L10.928 7.22888H13.2286C13.5449 7.22888 13.8037 7.49996 13.8037 7.83128V9.0361C13.8037 10.7002 12.5168 12.0481 10.928 12.0481H9.2025V13.957C9.2025 14.2319 8.99041 14.4578 8.7244 14.4578C8.6597 14.4578 8.59499 14.4427 8.53748 14.4163L4.98949 12.8237C4.75224 12.7183 4.60126 12.4736 4.60126 12.2063C4.60126 12.1008 4.62283 11.9992 4.66956 11.9051L5.20877 10.7755ZM5.17641 6.02407H6.90188V8.43369C6.90188 9.1001 6.38783 9.6385 5.75157 9.6385C5.1153 9.6385 4.60126 9.1001 4.60126 8.43369V6.62647C4.60126 6.29515 4.86008 6.02407 5.17641 6.02407ZM9.77765 7.83128C9.77765 7.67152 9.71706 7.51829 9.6092 7.40532C9.50133 7.29235 9.35504 7.22888 9.2025 7.22888C9.04996 7.22888 8.90367 7.29235 8.7958 7.40532C8.68794 7.51829 8.62734 7.67152 8.62734 7.83128C8.62734 7.99105 8.68794 8.14428 8.7958 8.25725C8.90367 8.37022 9.04996 8.43369 9.2025 8.43369C9.35504 8.43369 9.50133 8.37022 9.6092 8.25725C9.71706 8.14428 9.77765 7.99105 9.77765 7.83128Z" fill="#CBDCE8"/></svg>';

$favArrow = '<svg width="14" height="12" viewBox="0 0 14 12" fill="none" aria-hidden="true"><path d="M1 6h11M8 1.5 12.5 6 8 10.5" stroke="#fff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$favBookIcon = '<svg width="16" height="16" viewBox="0 0 17 17" fill="none" aria-hidden="true"><path d="M2.3 15.5v-2.9h2.9" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><path d="M15.4 6.7a6.9 6.9 0 0 1-13.1 6.1M.7 9.4A6.9 6.9 0 0 1 13.8 3.3" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/><path d="M13.8.6v2.9h-2.9" stroke="currentColor" stroke-width="1.2" stroke-linecap="round"/></svg>';

$favPin = '<svg width="10" height="14" viewBox="0 0 10 14" fill="none" aria-hidden="true"><path d="M5 6.65a1.75 1.75 0 1 1 0-3.5 1.75 1.75 0 0 1 0 3.5zM5 0C2.24 0 0 2.2 0 4.9 0 8.58 5 14 5 14s5-5.42 5-9.1C10 2.2 7.76 0 5 0z" fill="#FFC97A"/></svg>';

$favStar = '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true"><path d="M6.13.66c.27-.88 1.47-.88 1.74 0l1.03 3.3c.12.39.47.66.87.66h3.31c.89 0 1.26 1.18.54 1.73l-2.68 2.03c-.32.24-.45.68-.33 1.07l1.03 3.29c.27.88-.7 1.61-1.41 1.07L7.54 11.8a.9.9 0 0 0-1.08 0L3.78 13.81c-.72.54-1.68-.19-1.41-1.07l1.03-3.29a.9.9 0 0 0-.33-1.07L.38 6.34c-.72-.54-.4-1.73.54-1.73h3.31c.4 0 .75-.27.87-.66L6.13.66z" fill="#FFC97A"/></svg>';

$favAmenity = '
<span class="fav-card__amenity fav-card__amenity--blue" aria-hidden="true">
<svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
  <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white"/>
</svg>
</span>
<span class="fav-card__amenity fav-card__amenity--coral" aria-hidden="true">
<svg xmlns="http://www.w3.org/2000/svg" width="9" height="11" viewBox="0 0 9 11" fill="none">
  <path fill-rule="evenodd" clip-rule="evenodd" d="M3.79701 0.30821C3.81025 0.23997 3.84191 0.176658 3.88858 0.125138C3.93524 0.0736173 3.99512 0.0358558 4.06172 0.0159479C4.12832 -0.00396001 4.1991 -0.00525449 4.26638 0.0122049C4.33367 0.0296642 4.39489 0.0652111 4.4434 0.114991C4.56671 0.240837 4.80378 0.489988 5.04467 0.777274C5.28111 1.05884 5.53916 1.39761 5.68788 1.69951C5.8328 1.99443 5.98661 2.37578 6.10801 2.69421L6.67305 1.75354C6.70456 1.701 6.74826 1.65683 6.80046 1.62476C6.85265 1.59269 6.91181 1.57367 6.97292 1.56931C7.03402 1.56494 7.09528 1.57536 7.1515 1.59969C7.20773 1.62401 7.25727 1.66152 7.29592 1.70905C8.09867 2.70057 8.49846 3.76263 8.6974 4.57365C8.79718 4.97979 8.8474 5.32491 8.87282 5.57025C8.88576 5.69278 8.89424 5.81574 8.89824 5.93889V5.97131C8.89824 8.4482 6.93364 10.4649 4.44785 10.4649C1.96206 10.4649 0 8.44756 0 5.97004C0 5.28805 0.322244 3.68192 1.27563 2.36498C1.31266 2.31422 1.36166 2.27341 1.41826 2.24615C1.47487 2.2189 1.53734 2.20605 1.60011 2.20876C1.66287 2.21146 1.724 2.22963 1.77806 2.26166C1.83211 2.29368 1.87741 2.33856 1.90994 2.39231L2.55507 3.46709C2.75083 3.16073 3.01269 2.73044 3.21163 2.3332C3.49765 1.76117 3.72455 0.682572 3.79701 0.308845M4.3201 0.912655C4.20506 1.42113 4.01501 2.14697 3.77985 2.61858C3.46714 3.24336 3.0165 3.9298 2.86142 4.16051C2.82554 4.21345 2.77693 4.25651 2.72005 4.28574C2.66317 4.31497 2.59986 4.32943 2.53593 4.32778C2.472 4.32614 2.40952 4.30844 2.35422 4.27632C2.29892 4.24421 2.25258 4.1987 2.21948 4.14399L1.57118 3.06476C0.87457 4.19166 0.635589 5.45839 0.635589 5.97131C0.635589 8.10561 2.32244 9.82806 4.44785 9.82806C6.57326 9.82806 8.26265 8.10561 8.26265 5.97131V5.95351L8.26011 5.88995C8.25568 5.80484 8.24911 5.71986 8.24041 5.63508C8.20712 5.3287 8.15362 5.02487 8.08024 4.72555C7.87826 3.89157 7.52027 3.10334 7.02516 2.40248L6.38068 3.47535C6.34338 3.53732 6.28925 3.58743 6.22459 3.61985C6.15993 3.65227 6.08739 3.66566 6.01542 3.65847C5.94344 3.65128 5.87499 3.6238 5.81802 3.57923C5.76105 3.53466 5.71791 3.47483 5.6936 3.40671C5.59064 3.11815 5.33831 2.42917 5.11713 1.98044C5.00463 1.751 4.78916 1.46117 4.55781 1.18596C4.4801 1.09353 4.40086 1.00242 4.3201 0.912655Z" fill="#FEFEFE"/>
</svg>
</span>
<span class="fav-card__amenity fav-card__amenity--green" aria-hidden="true">
<svg xmlns="http://www.w3.org/2000/svg" width="8" height="10" viewBox="0 0 8 10" fill="none">
  <path d="M4 7.61905C3.73478 7.61905 3.48043 7.51871 3.29289 7.3401C3.10536 7.1615 3 6.91925 3 6.66667C3 6.1381 3.445 5.71429 4 5.71429C4.26522 5.71429 4.51957 5.81463 4.70711 5.99323C4.89464 6.17184 5 6.41408 5 6.66667C5 6.91925 4.89464 7.1615 4.70711 7.3401C4.51957 7.51871 4.26522 7.61905 4 7.61905ZM7 9.04762V4.28571H1V9.04762H7ZM7 3.33333C7.26522 3.33333 7.51957 3.43367 7.70711 3.61228C7.89464 3.79089 8 4.03313 8 4.28571V9.04762C8 9.30021 7.89464 9.54245 7.70711 9.72105C7.51957 9.89966 7.26522 10 7 10H1C0.734784 10 0.48043 9.89966 0.292893 9.72105C0.105357 9.54245 0 9.30021 0 9.04762V4.28571C0 3.75714 0.445 3.33333 1 3.33333H1.5V2.38095C1.5 1.74948 1.76339 1.14388 2.23223 0.697365C2.70107 0.25085 3.33696 0 4 0C4.3283 0 4.65339 0.0615852 4.95671 0.181239C5.26002 0.300893 5.53562 0.476273 5.76777 0.697365C5.99991 0.918457 6.18406 1.18093 6.3097 1.4698C6.43534 1.75867 6.5 2.06828 6.5 2.38095V3.33333H7ZM4 0.952381C3.60218 0.952381 3.22064 1.10289 2.93934 1.3708C2.65804 1.63871 2.5 2.00207 2.5 2.38095V3.33333H5.5V2.38095C5.5 2.00207 5.34196 1.63871 5.06066 1.3708C4.77936 1.10289 4.39782 0.952381 4 0.952381Z" fill="white"/>
</svg>
</span>
';

$favCheck = '<svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true"><path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>';

$favSpaceFeatures = '
<div class="fav-card__features">
  <span class="fav-card__feature">' . $favCheck . ' Grooming Table</span>
  <span class="fav-card__feature">' . $favCheck . ' Bath</span>
  <span class="fav-card__feature">' . $favCheck . ' Dryer</span>
  <span class="fav-card__feature">' . $favCheck . ' Parking</span>
  <span class="fav-card__feature fav-card__feature--more">+ 2</span>
</div>
';
?>

<div class="fav" id="favourites-root">
    <div class="fav-head">
        <h2 class="fav-title" id="fav-title">Favourite Groomers</h2>
        <div class="fav-toolbar">
            <div class="fav-tabs" role="tablist" aria-label="Favourites type">
                <button type="button" class="fav-tab is-active" role="tab" aria-selected="true" data-fav-tab="groomers">Groomers</button>
                <button type="button" class="fav-tab" role="tab" aria-selected="false" data-fav-tab="spaces">Space Hosts</button>
            </div>
            <label class="fav-search">
                <input type="search" id="fav-search" placeholder="Type to search..." autocomplete="off">
                <svg class="fav-search__icon" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                    <circle cx="6.2" cy="6.2" r="5.2" stroke="#9D9B98" />
                    <path d="m10.2 10.2 4.8 4.8" stroke="#9D9B98" stroke-linecap="round" />
                </svg>
            </label>
        </div>
    </div>

    <!-- Groomers -->
    <div class="fav-panel" id="fav-panel-groomers" data-fav-panel="groomers">
        <div class="fav-slider" id="fav-groomers-slider">
            <button type="button" class="fav-slider__arrow fav-slider__arrow--prev is-hidden" id="fav-groomers-prev" aria-label="Previous groomers">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" aria-hidden="true">
                    <path d="M7 1L1 7l6 6" stroke="#3B3731" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="fav-grid" id="fav-groomers-grid">

                <article class="fav-card" data-fav-name="Sarah W. Sarah's Grooming Studio" data-booked="1">
                    <div class="fav-card__body">
                        <div class="fav-card__media">
                            <div class="fav-card__badge" title="Verified"><?= $favShieldGroomer ?></div>
                            <svg class="fav-card__photo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130" fill="none" role="img" aria-label="Sarah W.">
                                <defs>
                                    <pattern id="fav-pattern-1" patternUnits="userSpaceOnUse" width="255" height="130">
                                        <image href="{{ rtrim(asset(''), '/') }}/images/profile_modal_image.jpg" width="255" height="130" preserveAspectRatio="xMinYMax slice"></image>
                                    </pattern>
                                </defs>
                                <path d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z" fill="url(#fav-pattern-1)"></path>
                            </svg>
                            <button type="button" class="fav-card__delete" aria-label="Remove favourite" data-fav-delete><?= $favTrash ?></button>
                        </div>
                        <div class="fav-card__amenities"><?= $favAmenity ?></div>
                        <span class="fav-card__tag">Groomer's Studio</span>
                        <h3 class="fav-card__name">Sarah W.</h3>
                        <p class="fav-card__sub">Sarah's Grooming Studio</p>
                        <div class="fav-card__meta">
                            <span><?= $favPin ?> 2.5 mi</span>
                            <span><?= $favStar ?> 4.3 <strong>(20)</strong></span>
                        </div>
                        <p class="fav-card__quote">"Hands down the best groomer we've tried. The studio is spotless..."</p>
                        <div class="fav-card__price-row">
                            <p class="fav-card__price">From <strong>£38</strong></p>
                            <a class="fav-card__go" href="{{ rtrim(asset(''), '/') }}/profiles/groomer/groomer_profile" aria-label="View profile"><?= $favArrow ?></a>
                        </div>
                    </div>
                    <a class="fav-card__cta fav-card__cta--book" href="{{ rtrim(asset(''), '/') }}/profiles/groomer/groomer_profile#booking-sidebar"><?= $favBookIcon ?> Book Again</a>
                    <div class="fav-card__confirm" hidden>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
                                <path d="M20.5981 21H2.6817C2.3907 20.9999 2.1047 20.9244 1.85164 20.7807C1.59858 20.6371 1.38712 20.4302 1.23792 20.1804C1.08872 19.9306 1.00688 19.6463 1.00042 19.3554C0.993947 19.0645 1.06306 18.7768 1.20101 18.5206L10.1587 1.88463C10.7942 0.705125 12.4856 0.705125 13.1211 1.88463L22.0788 18.5206C22.2168 18.7768 22.2859 19.0645 22.2794 19.3554C22.2729 19.6463 22.1911 19.9306 22.0419 20.1804C21.8927 20.4302 21.6812 20.6371 21.4282 20.7807C21.1751 20.9244 20.8891 20.9999 20.5981 21Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.6398 14.2264L11.3381 7.81374C11.3366 7.77346 11.3432 7.7333 11.3575 7.69562C11.3718 7.65794 11.3935 7.62351 11.4214 7.59435C11.4492 7.5652 11.4826 7.54192 11.5196 7.52589C11.5565 7.50985 11.5963 7.50139 11.6366 7.50099C11.6777 7.50059 11.7183 7.50856 11.7562 7.52441C11.794 7.54026 11.8282 7.56366 11.8567 7.59318C11.8852 7.6227 11.9073 7.65771 11.9218 7.69609C11.9363 7.73446 11.9428 7.77539 11.941 7.81637L11.6398 14.2264Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.6401 18.4248C11.4322 18.4248 11.229 18.3631 11.0561 18.2476C10.8832 18.1321 10.7485 17.9679 10.6689 17.7758C10.5893 17.5837 10.5685 17.3723 10.6091 17.1684C10.6496 16.9645 10.7498 16.7772 10.8968 16.6302C11.0438 16.4831 11.2311 16.383 11.435 16.3425C11.6389 16.3019 11.8503 16.3227 12.0424 16.4023C12.2345 16.4819 12.3987 16.6166 12.5142 16.7895C12.6297 16.9623 12.6914 17.1656 12.6914 17.3735C12.6914 17.6523 12.5806 17.9197 12.3835 18.1169C12.1863 18.314 11.9189 18.4248 11.6401 18.4248Z" fill="#FF6E6E" />
                            </svg>
                            <h4>Remove this groomer?</h4>
                            <p>Are you sure you want to delete this groomer from your favourites?</p>
                        </div>

                        <div class="fav-card__confirm-actions">
                            <button type="button" class="fav-card__btn fav-card__btn--ghost" data-fav-cancel>Cancel</button>
                            <button type="button" class="fav-card__btn fav-card__btn--danger" data-fav-confirm-delete>Yes, delete</button>
                        </div>
                    </div>
                </article>

                <article class="fav-card" data-fav-name="Cathy P. Cathy's Pawfect Studio" data-booked="0">
                    <div class="fav-card__body">
                        <div class="fav-card__media">
                            <div class="fav-card__badge" title="Verified"><?= $favShieldGroomer ?></div>
                            <svg class="fav-card__photo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130" fill="none" role="img" aria-label="Cathy P.">
                                <defs>
                                    <pattern id="fav-pattern-2" patternUnits="userSpaceOnUse" width="255" height="130">
                                        <image href="{{ rtrim(asset(''), '/') }}/images/profile_modal_image2.jpg" width="255" height="130" preserveAspectRatio="xMinYMax slice"></image>
                                    </pattern>
                                </defs>
                                <path d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z" fill="url(#fav-pattern-2)"></path>
                            </svg>
                            <button type="button" class="fav-card__delete" aria-label="Remove favourite" data-fav-delete><?= $favTrash ?></button>
                        </div>
                        <div class="fav-card__amenities"><?= $favAmenity ?></div>
                        <span class="fav-card__tag fav-card__tag--peach">Salons</span>
                        <h3 class="fav-card__name">Cathy P.</h3>
                        <p class="fav-card__sub">Cathy's Pawfect Studio</p>
                        <div class="fav-card__meta">
                            <span><?= $favPin ?> 1.5 mi</span>
                            <span><?= $favStar ?> 4.6 <strong>(34)</strong></span>
                        </div>
                        <p class="fav-card__quote">"Such a calming experience for my anxious pup. Cathy is amazing!"</p>
                        <div class="fav-card__price-row">
                            <p class="fav-card__price">From <strong>£42</strong></p>
                            <a class="fav-card__go" href="{{ rtrim(asset(''), '/') }}/profiles/groomer/groomer_profile" aria-label="View profile"><?= $favArrow ?></a>
                        </div>
                    </div>
                    <a class="fav-card__cta fav-card__cta--view" href="{{ rtrim(asset(''), '/') }}/profiles/groomer/groomer_profile"><?= $favArrow ?> View Profile</a>
                    <div class="fav-card__confirm" hidden>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
                                <path d="M20.5981 21H2.6817C2.3907 20.9999 2.1047 20.9244 1.85164 20.7807C1.59858 20.6371 1.38712 20.4302 1.23792 20.1804C1.08872 19.9306 1.00688 19.6463 1.00042 19.3554C0.993947 19.0645 1.06306 18.7768 1.20101 18.5206L10.1587 1.88463C10.7942 0.705125 12.4856 0.705125 13.1211 1.88463L22.0788 18.5206C22.2168 18.7768 22.2859 19.0645 22.2794 19.3554C22.2729 19.6463 22.1911 19.9306 22.0419 20.1804C21.8927 20.4302 21.6812 20.6371 21.4282 20.7807C21.1751 20.9244 20.8891 20.9999 20.5981 21Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.6398 14.2264L11.3381 7.81374C11.3366 7.77346 11.3432 7.7333 11.3575 7.69562C11.3718 7.65794 11.3935 7.62351 11.4214 7.59435C11.4492 7.5652 11.4826 7.54192 11.5196 7.52589C11.5565 7.50985 11.5963 7.50139 11.6366 7.50099C11.6777 7.50059 11.7183 7.50856 11.7562 7.52441C11.794 7.54026 11.8282 7.56366 11.8567 7.59318C11.8852 7.6227 11.9073 7.65771 11.9218 7.69609C11.9363 7.73446 11.9428 7.77539 11.941 7.81637L11.6398 14.2264Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.6401 18.4248C11.4322 18.4248 11.229 18.3631 11.0561 18.2476C10.8832 18.1321 10.7485 17.9679 10.6689 17.7758C10.5893 17.5837 10.5685 17.3723 10.6091 17.1684C10.6496 16.9645 10.7498 16.7772 10.8968 16.6302C11.0438 16.4831 11.2311 16.383 11.435 16.3425C11.6389 16.3019 11.8503 16.3227 12.0424 16.4023C12.2345 16.4819 12.3987 16.6166 12.5142 16.7895C12.6297 16.9623 12.6914 17.1656 12.6914 17.3735C12.6914 17.6523 12.5806 17.9197 12.3835 18.1169C12.1863 18.314 11.9189 18.4248 11.6401 18.4248Z" fill="#FF6E6E" />
                            </svg>
                            <h4>Remove this groomer?</h4>
                            <p>Are you sure you want to delete this groomer from your favourites?</p>
                        </div>
                        <div class="fav-card__confirm-actions">
                            <button type="button" class="fav-card__btn fav-card__btn--ghost" data-fav-cancel>Cancel</button>
                            <button type="button" class="fav-card__btn fav-card__btn--danger" data-fav-confirm-delete>Yes, delete</button>
                        </div>
                    </div>
                </article>

                <article class="fav-card" data-fav-name="Mia L. Mia's Mobile Grooming" data-booked="1">
                    <div class="fav-card__body">
                        <div class="fav-card__media">
                            <div class="fav-card__badge" title="Verified"><?= $favShieldGroomer ?></div>
                            <svg class="fav-card__photo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130" fill="none" role="img" aria-label="Mia L.">
                                <defs>
                                    <pattern id="fav-pattern-3" patternUnits="userSpaceOnUse" width="255" height="130">
                                        <image href="{{ rtrim(asset(''), '/') }}/images/profile_modal_image3.jpg" width="255" height="130" preserveAspectRatio="xMinYMax slice"></image>
                                    </pattern>
                                </defs>
                                <path d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z" fill="url(#fav-pattern-3)"></path>
                            </svg>
                            <button type="button" class="fav-card__delete" aria-label="Remove favourite" data-fav-delete><?= $favTrash ?></button>
                        </div>
                        <div class="fav-card__amenities"><?= $favAmenity ?></div>
                        <span class="fav-card__tag">Mobile Station</span>
                        <h3 class="fav-card__name">Mia L.</h3>
                        <p class="fav-card__sub">Mia's Mobile Grooming</p>
                        <div class="fav-card__meta">
                            <span><?= $favPin ?> 3.1 mi</span>
                            <span><?= $favStar ?> 4.8 <strong>(51)</strong></span>
                        </div>
                        <p class="fav-card__quote">"Comes to our door and my dog looks fabulous every time."</p>
                        <div class="fav-card__price-row">
                            <p class="fav-card__price">From <strong>£45</strong></p>
                            <a class="fav-card__go" href="{{ rtrim(asset(''), '/') }}/profiles/groomer/groomer_profile" aria-label="View profile"><?= $favArrow ?></a>
                        </div>
                    </div>
                    <a class="fav-card__cta fav-card__cta--book" href="{{ rtrim(asset(''), '/') }}/profiles/groomer/groomer_profile#booking-sidebar"><?= $favBookIcon ?> Book Again</a>
                    <div class="fav-card__confirm" hidden>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
                                <path d="M20.5981 21H2.6817C2.3907 20.9999 2.1047 20.9244 1.85164 20.7807C1.59858 20.6371 1.38712 20.4302 1.23792 20.1804C1.08872 19.9306 1.00688 19.6463 1.00042 19.3554C0.993947 19.0645 1.06306 18.7768 1.20101 18.5206L10.1587 1.88463C10.7942 0.705125 12.4856 0.705125 13.1211 1.88463L22.0788 18.5206C22.2168 18.7768 22.2859 19.0645 22.2794 19.3554C22.2729 19.6463 22.1911 19.9306 22.0419 20.1804C21.8927 20.4302 21.6812 20.6371 21.4282 20.7807C21.1751 20.9244 20.8891 20.9999 20.5981 21Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.6398 14.2264L11.3381 7.81374C11.3366 7.77346 11.3432 7.7333 11.3575 7.69562C11.3718 7.65794 11.3935 7.62351 11.4214 7.59435C11.4492 7.5652 11.4826 7.54192 11.5196 7.52589C11.5565 7.50985 11.5963 7.50139 11.6366 7.50099C11.6777 7.50059 11.7183 7.50856 11.7562 7.52441C11.794 7.54026 11.8282 7.56366 11.8567 7.59318C11.8852 7.6227 11.9073 7.65771 11.9218 7.69609C11.9363 7.73446 11.9428 7.77539 11.941 7.81637L11.6398 14.2264Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.6401 18.4248C11.4322 18.4248 11.229 18.3631 11.0561 18.2476C10.8832 18.1321 10.7485 17.9679 10.6689 17.7758C10.5893 17.5837 10.5685 17.3723 10.6091 17.1684C10.6496 16.9645 10.7498 16.7772 10.8968 16.6302C11.0438 16.4831 11.2311 16.383 11.435 16.3425C11.6389 16.3019 11.8503 16.3227 12.0424 16.4023C12.2345 16.4819 12.3987 16.6166 12.5142 16.7895C12.6297 16.9623 12.6914 17.1656 12.6914 17.3735C12.6914 17.6523 12.5806 17.9197 12.3835 18.1169C12.1863 18.314 11.9189 18.4248 11.6401 18.4248Z" fill="#FF6E6E" />
                            </svg>
                            <h4>Remove this groomer?</h4>
                            <p>Are you sure you want to delete this groomer from your favourites?</p>
                        </div>
                        <div class="fav-card__confirm-actions">
                            <button type="button" class="fav-card__btn fav-card__btn--ghost" data-fav-cancel>Cancel</button>
                            <button type="button" class="fav-card__btn fav-card__btn--danger" data-fav-confirm-delete>Yes, delete</button>
                        </div>
                    </div>
                </article>

                <article class="fav-card" data-fav-name="Ken T. Ken's Grooming Mobile" data-booked="0">
                    <div class="fav-card__body">
                        <div class="fav-card__media">
                            <div class="fav-card__badge" title="Verified"><?= $favShieldGroomer ?></div>
                            <svg class="fav-card__photo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130" fill="none" role="img" aria-label="Ken T.">
                                <defs>
                                    <pattern id="fav-pattern-4" patternUnits="userSpaceOnUse" width="255" height="130">
                                        <image href="{{ rtrim(asset(''), '/') }}/images/groomer-profile.png" width="255" height="130" preserveAspectRatio="xMinYMax slice"></image>
                                    </pattern>
                                </defs>
                                <path d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z" fill="url(#fav-pattern-4)"></path>
                            </svg>
                            <button type="button" class="fav-card__delete" aria-label="Remove favourite" data-fav-delete><?= $favTrash ?></button>
                        </div>
                        <div class="fav-card__amenities"><?= $favAmenity ?></div>
                        <span class="fav-card__tag">Home Visit</span>
                        <h3 class="fav-card__name">Ken T.</h3>
                        <p class="fav-card__sub">Ken's Grooming Mobile</p>
                        <div class="fav-card__meta">
                            <span><?= $favPin ?> 4.2 mi</span>
                            <span><?= $favStar ?> 4.5 <strong>(27)</strong></span>
                        </div>
                        <p class="fav-card__quote">"Reliable home visits — my dog settles quickly with Ken."</p>
                        <div class="fav-card__price-row">
                            <p class="fav-card__price">From <strong>£40</strong></p>
                            <a class="fav-card__go" href="{{ rtrim(asset(''), '/') }}/profiles/groomer/groomer_profile" aria-label="View profile"><?= $favArrow ?></a>
                        </div>
                    </div>
                    <a class="fav-card__cta fav-card__cta--view" href="{{ rtrim(asset(''), '/') }}/profiles/groomer/groomer_profile"><?= $favArrow ?> View Profile</a>
                    <div class="fav-card__confirm" hidden>
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
                                <path d="M20.5981 21H2.6817C2.3907 20.9999 2.1047 20.9244 1.85164 20.7807C1.59858 20.6371 1.38712 20.4302 1.23792 20.1804C1.08872 19.9306 1.00688 19.6463 1.00042 19.3554C0.993947 19.0645 1.06306 18.7768 1.20101 18.5206L10.1587 1.88463C10.7942 0.705125 12.4856 0.705125 13.1211 1.88463L22.0788 18.5206C22.2168 18.7768 22.2859 19.0645 22.2794 19.3554C22.2729 19.6463 22.1911 19.9306 22.0419 20.1804C21.8927 20.4302 21.6812 20.6371 21.4282 20.7807C21.1751 20.9244 20.8891 20.9999 20.5981 21Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.6398 14.2264L11.3381 7.81374C11.3366 7.77346 11.3432 7.7333 11.3575 7.69562C11.3718 7.65794 11.3935 7.62351 11.4214 7.59435C11.4492 7.5652 11.4826 7.54192 11.5196 7.52589C11.5565 7.50985 11.5963 7.50139 11.6366 7.50099C11.6777 7.50059 11.7183 7.50856 11.7562 7.52441C11.794 7.54026 11.8282 7.56366 11.8567 7.59318C11.8852 7.6227 11.9073 7.65771 11.9218 7.69609C11.9363 7.73446 11.9428 7.77539 11.941 7.81637L11.6398 14.2264Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M11.6401 18.4248C11.4322 18.4248 11.229 18.3631 11.0561 18.2476C10.8832 18.1321 10.7485 17.9679 10.6689 17.7758C10.5893 17.5837 10.5685 17.3723 10.6091 17.1684C10.6496 16.9645 10.7498 16.7772 10.8968 16.6302C11.0438 16.4831 11.2311 16.383 11.435 16.3425C11.6389 16.3019 11.8503 16.3227 12.0424 16.4023C12.2345 16.4819 12.3987 16.6166 12.5142 16.7895C12.6297 16.9623 12.6914 17.1656 12.6914 17.3735C12.6914 17.6523 12.5806 17.9197 12.3835 18.1169C12.1863 18.314 11.9189 18.4248 11.6401 18.4248Z" fill="#FF6E6E" />
                            </svg>
                            <h4>Remove this groomer?</h4>
                            <p>Are you sure you want to delete this groomer from your favourites?</p>
                        </div>
                        <div class="fav-card__confirm-actions">
                            <button type="button" class="fav-card__btn fav-card__btn--ghost" data-fav-cancel>Cancel</button>
                            <button type="button" class="fav-card__btn fav-card__btn--danger" data-fav-confirm-delete>Yes, delete</button>
                        </div>
                    </div>
                </article>

            </div>
            <button type="button" class="fav-slider__arrow fav-slider__arrow--next is-hidden" id="fav-groomers-next" aria-label="Next groomers">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" aria-hidden="true">
                    <path d="M1 1l6 6-6 6" stroke="#3B3731" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
        <p class="fav-empty" id="fav-groomers-empty" hidden>No favourite groomers match your search.</p>
    </div>

    <!-- Space Hosts -->
    <div class="fav-panel" id="fav-panel-spaces" data-fav-panel="spaces" hidden>
        <div class="fav-slider" id="fav-spaces-slider">
            <button type="button" class="fav-slider__arrow fav-slider__arrow--prev is-hidden" id="fav-spaces-prev" aria-label="Previous spaces">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" aria-hidden="true">
                    <path d="M7 1L1 7l6 6" stroke="#3B3731" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="fav-grid" id="fav-spaces-grid">

                <article class="fav-card" data-fav-name="Furs & Co. Studio Dev É." data-booked="1" data-fav-kind="space">
                    <div class="fav-card__body">
                        <div class="fav-card__media">
                            <div class="fav-card__badge" title="Verified"><?= $favShieldSpace ?></div>
                            <svg class="fav-card__photo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130" fill="none" role="img" aria-label="Furs & Co. Studio">
                                <defs>
                                    <pattern id="fav-pattern-5" patternUnits="userSpaceOnUse" width="255" height="130">
                                        <image href="{{ rtrim(asset(''), '/') }}/images/space_card1.png" width="255" height="130" preserveAspectRatio="xMinYMax slice"></image>
                                    </pattern>
                                </defs>
                                <path d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z" fill="url(#fav-pattern-5)"></path>
                            </svg>
                            <button type="button" class="fav-card__delete" aria-label="Remove favourite" data-fav-delete><?= $favTrash ?></button>
                        </div>
                        <div class="fav-card__amenities"><?= $favAmenity ?></div>
                        <span class="fav-card__tag fav-card__tag--peach">Salons</span>
                        <h3 class="fav-card__name">Furs &amp; Co. Studio</h3>
                        <p class="fav-card__sub">Hosted by <strong>Dev É.</strong></p>
                        <div class="fav-card__meta">
                            <span><?= $favPin ?> 1.2 mi</span>
                            <span><?= $favStar ?> 4.7 <strong>(18)</strong></span>
                        </div>
                        <p class="fav-card__quote">"Beautiful space — clean, calm, and perfect for full grooms."</p>
                        <?= $favSpaceFeatures ?>
                        <div class="fav-card__price-row">
                            <p class="fav-card__price">From <strong>£28</strong> / hour</p>
                            <a class="fav-card__go" href="{{ rtrim(asset(''), '/') }}/profiles/space/space_profile" aria-label="View profile"><?= $favArrow ?></a>
                        </div>
                    </div>
                    <a class="fav-card__cta fav-card__cta--book" href="{{ rtrim(asset(''), '/') }}/profiles/space/space_profile"><?= $favBookIcon ?> Book Again</a>
                    <div class="fav-card__confirm" hidden>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
                            <path d="M20.5981 21H2.6817C2.3907 20.9999 2.1047 20.9244 1.85164 20.7807C1.59858 20.6371 1.38712 20.4302 1.23792 20.1804C1.08872 19.9306 1.00688 19.6463 1.00042 19.3554C0.993947 19.0645 1.06306 18.7768 1.20101 18.5206L10.1587 1.88463C10.7942 0.705125 12.4856 0.705125 13.1211 1.88463L22.0788 18.5206C22.2168 18.7768 22.2859 19.0645 22.2794 19.3554C22.2729 19.6463 22.1911 19.9306 22.0419 20.1804C21.8927 20.4302 21.6812 20.6371 21.4282 20.7807C21.1751 20.9244 20.8891 20.9999 20.5981 21Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.6398 14.2264L11.3381 7.81374C11.3366 7.77346 11.3432 7.7333 11.3575 7.69562C11.3718 7.65794 11.3935 7.62351 11.4214 7.59435C11.4492 7.5652 11.4826 7.54192 11.5196 7.52589C11.5565 7.50985 11.5963 7.50139 11.6366 7.50099C11.6777 7.50059 11.7183 7.50856 11.7562 7.52441C11.794 7.54026 11.8282 7.56366 11.8567 7.59318C11.8852 7.6227 11.9073 7.65771 11.9218 7.69609C11.9363 7.73446 11.9428 7.77539 11.941 7.81637L11.6398 14.2264Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.6401 18.4248C11.4322 18.4248 11.229 18.3631 11.0561 18.2476C10.8832 18.1321 10.7485 17.9679 10.6689 17.7758C10.5893 17.5837 10.5685 17.3723 10.6091 17.1684C10.6496 16.9645 10.7498 16.7772 10.8968 16.6302C11.0438 16.4831 11.2311 16.383 11.435 16.3425C11.6389 16.3019 11.8503 16.3227 12.0424 16.4023C12.2345 16.4819 12.3987 16.6166 12.5142 16.7895C12.6297 16.9623 12.6914 17.1656 12.6914 17.3735C12.6914 17.6523 12.5806 17.9197 12.3835 18.1169C12.1863 18.314 11.9189 18.4248 11.6401 18.4248Z" fill="#FF6E6E" />
                        </svg>
                        <h4>Remove this space?</h4>
                        <p>Are you sure you want to delete this space from your favourites?</p>
                        <div class="fav-card__confirm-actions">
                            <button type="button" class="fav-card__btn fav-card__btn--ghost" data-fav-cancel>Cancel</button>
                            <button type="button" class="fav-card__btn fav-card__btn--danger" data-fav-confirm-delete>Yes, delete</button>
                        </div>
                    </div>
                </article>

                <article class="fav-card" data-fav-name="Garden Space Hosted by Alex" data-booked="0" data-fav-kind="space">
                    <div class="fav-card__body">
                        <div class="fav-card__media">
                            <div class="fav-card__badge" title="Verified"><?= $favShieldSpace ?></div>
                            <svg class="fav-card__photo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130" fill="none" role="img" aria-label="Garden Space">
                                <defs>
                                    <pattern id="fav-pattern-6" patternUnits="userSpaceOnUse" width="255" height="130">
                                        <image href="{{ rtrim(asset(''), '/') }}/images/space_card2.png" width="255" height="130" preserveAspectRatio="xMinYMax slice"></image>
                                    </pattern>
                                </defs>
                                <path d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z" fill="url(#fav-pattern-6)"></path>
                            </svg>
                            <button type="button" class="fav-card__delete" aria-label="Remove favourite" data-fav-delete><?= $favTrash ?></button>
                        </div>
                        <div class="fav-card__amenities"><?= $favAmenity ?></div>
                        <span class="fav-card__tag">Garden/Shed</span>
                        <h3 class="fav-card__name">Garden Space</h3>
                        <p class="fav-card__sub">Hosted by <strong>Alex R.</strong></p>
                        <div class="fav-card__meta">
                            <span><?= $favPin ?> 2.8 mi</span>
                            <span><?= $favStar ?> 4.4 <strong>(12)</strong></span>
                        </div>
                        <p class="fav-card__quote">"Quiet garden studio — my clients love the outdoor drying area."</p>
                        <?= $favSpaceFeatures ?>
                        <div class="fav-card__price-row">
                            <p class="fav-card__price">From <strong>£22</strong> / hour</p>
                            <a class="fav-card__go" href="{{ rtrim(asset(''), '/') }}/profiles/space/space_profile" aria-label="View profile"><?= $favArrow ?></a>
                        </div>
                    </div>
                    <a class="fav-card__cta fav-card__cta--view" href="{{ rtrim(asset(''), '/') }}/profiles/space/space_profile"><?= $favArrow ?> View Profile</a>
                    <div class="fav-card__confirm" hidden>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
                            <path d="M20.5981 21H2.6817C2.3907 20.9999 2.1047 20.9244 1.85164 20.7807C1.59858 20.6371 1.38712 20.4302 1.23792 20.1804C1.08872 19.9306 1.00688 19.6463 1.00042 19.3554C0.993947 19.0645 1.06306 18.7768 1.20101 18.5206L10.1587 1.88463C10.7942 0.705125 12.4856 0.705125 13.1211 1.88463L22.0788 18.5206C22.2168 18.7768 22.2859 19.0645 22.2794 19.3554C22.2729 19.6463 22.1911 19.9306 22.0419 20.1804C21.8927 20.4302 21.6812 20.6371 21.4282 20.7807C21.1751 20.9244 20.8891 20.9999 20.5981 21Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.6398 14.2264L11.3381 7.81374C11.3366 7.77346 11.3432 7.7333 11.3575 7.69562C11.3718 7.65794 11.3935 7.62351 11.4214 7.59435C11.4492 7.5652 11.4826 7.54192 11.5196 7.52589C11.5565 7.50985 11.5963 7.50139 11.6366 7.50099C11.6777 7.50059 11.7183 7.50856 11.7562 7.52441C11.794 7.54026 11.8282 7.56366 11.8567 7.59318C11.8852 7.6227 11.9073 7.65771 11.9218 7.69609C11.9363 7.73446 11.9428 7.77539 11.941 7.81637L11.6398 14.2264Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.6401 18.4248C11.4322 18.4248 11.229 18.3631 11.0561 18.2476C10.8832 18.1321 10.7485 17.9679 10.6689 17.7758C10.5893 17.5837 10.5685 17.3723 10.6091 17.1684C10.6496 16.9645 10.7498 16.7772 10.8968 16.6302C11.0438 16.4831 11.2311 16.383 11.435 16.3425C11.6389 16.3019 11.8503 16.3227 12.0424 16.4023C12.2345 16.4819 12.3987 16.6166 12.5142 16.7895C12.6297 16.9623 12.6914 17.1656 12.6914 17.3735C12.6914 17.6523 12.5806 17.9197 12.3835 18.1169C12.1863 18.314 11.9189 18.4248 11.6401 18.4248Z" fill="#FF6E6E" />
                        </svg>
                        <h4>Remove this space?</h4>
                        <p>Are you sure you want to delete this space from your favourites?</p>
                        <div class="fav-card__confirm-actions">
                            <button type="button" class="fav-card__btn fav-card__btn--ghost" data-fav-cancel>Cancel</button>
                            <button type="button" class="fav-card__btn fav-card__btn--danger" data-fav-confirm-delete>Yes, delete</button>
                        </div>
                    </div>
                </article>

                <article class="fav-card" data-fav-name="Central Salon Bay Hosted by Priya" data-booked="1" data-fav-kind="space">
                    <div class="fav-card__body">
                        <div class="fav-card__media">
                            <div class="fav-card__badge" title="Verified"><?= $favShieldSpace ?></div>
                            <svg class="fav-card__photo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130" fill="none" role="img" aria-label="Central Salon Bay">
                                <defs>
                                    <pattern id="fav-pattern-7" patternUnits="userSpaceOnUse" width="255" height="130">
                                        <image href="{{ rtrim(asset(''), '/') }}/images/space_card3.png" width="255" height="130" preserveAspectRatio="xMinYMax slice"></image>
                                    </pattern>
                                </defs>
                                <path d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z" fill="url(#fav-pattern-7)"></path>
                            </svg>
                            <button type="button" class="fav-card__delete" aria-label="Remove favourite" data-fav-delete><?= $favTrash ?></button>
                        </div>
                        <div class="fav-card__amenities"><?= $favAmenity ?></div>
                        <span class="fav-card__tag fav-card__tag--peach">Private rooms</span>
                        <h3 class="fav-card__name">Central Salon Bay</h3>
                        <p class="fav-card__sub">Hosted by <strong>Priya S.</strong></p>
                        <div class="fav-card__meta">
                            <span><?= $favPin ?> 0.9 mi</span>
                            <span><?= $favStar ?> 4.9 <strong>(40)</strong></span>
                        </div>
                        <p class="fav-card__quote">"Professional setup in a great location — highly recommend."</p>
                        <?= $favSpaceFeatures ?>
                        <div class="fav-card__price-row">
                            <p class="fav-card__price">From <strong>£35</strong> / hour</p>
                            <a class="fav-card__go" href="{{ rtrim(asset(''), '/') }}/profiles/space/space_profile" aria-label="View profile"><?= $favArrow ?></a>
                        </div>
                    </div>
                    <a class="fav-card__cta fav-card__cta--book" href="{{ rtrim(asset(''), '/') }}/profiles/space/space_profile"><?= $favBookIcon ?> Book Again</a>
                    <div class="fav-card__confirm" hidden>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="22" viewBox="0 0 24 22" fill="none">
                            <path d="M20.5981 21H2.6817C2.3907 20.9999 2.1047 20.9244 1.85164 20.7807C1.59858 20.6371 1.38712 20.4302 1.23792 20.1804C1.08872 19.9306 1.00688 19.6463 1.00042 19.3554C0.993947 19.0645 1.06306 18.7768 1.20101 18.5206L10.1587 1.88463C10.7942 0.705125 12.4856 0.705125 13.1211 1.88463L22.0788 18.5206C22.2168 18.7768 22.2859 19.0645 22.2794 19.3554C22.2729 19.6463 22.1911 19.9306 22.0419 20.1804C21.8927 20.4302 21.6812 20.6371 21.4282 20.7807C21.1751 20.9244 20.8891 20.9999 20.5981 21Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.6398 14.2264L11.3381 7.81374C11.3366 7.77346 11.3432 7.7333 11.3575 7.69562C11.3718 7.65794 11.3935 7.62351 11.4214 7.59435C11.4492 7.5652 11.4826 7.54192 11.5196 7.52589C11.5565 7.50985 11.5963 7.50139 11.6366 7.50099C11.6777 7.50059 11.7183 7.50856 11.7562 7.52441C11.794 7.54026 11.8282 7.56366 11.8567 7.59318C11.8852 7.6227 11.9073 7.65771 11.9218 7.69609C11.9363 7.73446 11.9428 7.77539 11.941 7.81637L11.6398 14.2264Z" stroke="#FF6E6E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M11.6401 18.4248C11.4322 18.4248 11.229 18.3631 11.0561 18.2476C10.8832 18.1321 10.7485 17.9679 10.6689 17.7758C10.5893 17.5837 10.5685 17.3723 10.6091 17.1684C10.6496 16.9645 10.7498 16.7772 10.8968 16.6302C11.0438 16.4831 11.2311 16.383 11.435 16.3425C11.6389 16.3019 11.8503 16.3227 12.0424 16.4023C12.2345 16.4819 12.3987 16.6166 12.5142 16.7895C12.6297 16.9623 12.6914 17.1656 12.6914 17.3735C12.6914 17.6523 12.5806 17.9197 12.3835 18.1169C12.1863 18.314 11.9189 18.4248 11.6401 18.4248Z" fill="#FF6E6E" />
                        </svg>
                        <h4>Remove this space?</h4>
                        <p>Are you sure you want to delete this space from your favourites?</p>
                        <div class="fav-card__confirm-actions">
                            <button type="button" class="fav-card__btn fav-card__btn--ghost" data-fav-cancel>Cancel</button>
                            <button type="button" class="fav-card__btn fav-card__btn--danger" data-fav-confirm-delete>Yes, delete</button>
                        </div>
                    </div>
                </article>

            </div>
            <button type="button" class="fav-slider__arrow fav-slider__arrow--next is-hidden" id="fav-spaces-next" aria-label="Next spaces">
                <svg width="8" height="14" viewBox="0 0 8 14" fill="none" aria-hidden="true">
                    <path d="M1 1l6 6-6 6" stroke="#3B3731" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
        <p class="fav-empty" id="fav-spaces-empty" hidden>No favourite spaces match your search.</p>
    </div>
</div>
