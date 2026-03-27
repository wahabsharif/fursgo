// document.addEventListener('DOMContentLoaded', function () {
//     // Initialize Leaflet map globally
//     window.map = L.map('map', {
//         zoomControl: true,
//         attributionControl: false,
//         preferCanvas: true
//     });

//     // Grey base map
//     L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
//         subdomains: 'abcd', maxZoom: 20
//     }).addTo(window.map);

//     // Labels overlay
//     L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
//         subdomains: 'abcd', maxZoom: 20, pane: 'overlayPane'
//     }).addTo(window.map);

//     // Yellow pin
//     const yellowPin = L.icon({
//         iconUrl: 'data:image/svg+xml;utf8,' + encodeURIComponent(`
//             <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
//                 <path fill="#F5C400" d="M12 2C8.1 2 5 5.1 5 9c0 5.2 7 13 7 13s7-7.8 7-13c0-3.9-3.1-7-7-7zm0 9.5c-1.4 0-2.5-1.1-2.5-2.5S10.6 6.5 12 6.5s2.5 1.1 2.5 2.5S13.4 11.5 12 11.5z"/>
//             </svg>
//         `),
//         iconSize: [28, 28],
//         iconAnchor: [14, 28],
//         popupAnchor: [0, -26]
//     });

//     // Locations
//     const locations = [
//         { name: 'Waterloo Station', lat: 51.5033, lng: -0.1147 },
//         { name: 'Westminster', lat: 51.4995, lng: -0.1248 },
//         { name: 'Strand', lat: 51.511227, lng: -0.119470 }
//     ];

//     // Add markers now
//     locations.forEach(loc => {
//         L.marker([loc.lat, loc.lng], { icon: yellowPin })
//             .addTo(window.map)
//             .bindPopup(`<strong>${loc.name}</strong>`);
//     });

//     // ---- Custom Tabs ----
//     document.querySelectorAll('.tabs').forEach(tabSection => {
//         const buttons = tabSection.querySelectorAll('.tablinks');
//         const contents = tabSection.querySelectorAll('.tabcontent');

//         function activateTab(tabName) {
//             contents.forEach(c => {
//                 const isActive = c.dataset.tabContent === tabName;
//                 c.style.display = isActive ? 'block' : 'none';

//                 // Only when map tab is active
//                 if (isActive && tabName === 'groomer-map-view' && window.map) {
//                     setTimeout(() => {
//                         // Force map resize
//                         window.map.invalidateSize();

//                         // Fit all markers properly now that container is visible
//                         window.map.invalidateSize();

//                             window.map.fitBounds(
//                                 locations.map(l => [l.lat, l.lng]),
//                                 {
//                                     padding: [40, 40],
//                                     maxZoom: 15
//                                 }
//                             );

//                     }, 100);
//                 }
//             });

//             buttons.forEach(b => {
//                 b.classList.toggle('active', b.dataset.tab === tabName);
//             });
//         }

//         buttons.forEach(button => {
//             button.addEventListener('click', () => {
//                 activateTab(button.dataset.tab);
//             });
//         });

//         // Auto-activate first tab
//         if (buttons.length) {
//             activateTab(buttons[0].dataset.tab);
//         }
//     });
// });

function groomerTooltipCardSVG(imageUrl, clipId) {
    return `
    <svg xmlns="http://www.w3.org/2000/svg"
         width="60"
         height="60"
         viewBox="0 0 170 246"
         style="display:block;">
        <defs>
            <clipPath id="${clipId}">
                <path d="M165 0C167.761 0 170 2.23858 170 5V241C170 243.761 167.761 246 165 246H5C2.23858 246 0 243.761 0 241V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H165Z"/>
            </clipPath>
        </defs>

        <image
            href="${imageUrl}"
            width="170"
            height="246"
            preserveAspectRatio="xMidYMid slice"
            clip-path="url(#${clipId})" />
    </svg>`;
}

function spaceTooltipCardSVG(imageUrl, clipId) {
    return `
    <svg xmlns="http://www.w3.org/2000/svg"
         width="65"
         height="65"
         viewBox="0 0 170 246"
         style="display:block;">
        <defs>
            <clipPath id="${clipId}">
                <path d="M165 0C167.761 0 170 2.23858 170 5V241C170 243.761 167.761 246 165 246H5C2.23858 246 0 243.761 0 241V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H165Z"/>
            </clipPath>
        </defs>

        <image
            href="${imageUrl}"
            width="170"
            height="246"
            preserveAspectRatio="xMidYMid slice"
            clip-path="url(#${clipId})" />
    </svg>`;
}

// New custom SVG function
function customTooltipSVG(color = '#C9DDA0', width = 21, height = 22) {
    return `
    <div class="custom-svg-wrapper" style="width:${width}px; height:${height}px; display:flex; align-items:center; justify-content:center;">
        <svg xmlns="http://www.w3.org/2000/svg" width="${width}" height="${height}" viewBox="0 0 21 22" fill="none">
            <rect x="4.14746" y="4.14746" width="12.443" height="13.8256"
                  rx="3" fill="white" />
            <path d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z"
                  fill="${color}" />
        </svg>
    </div>
    `;
}

function spaceCustomTooltipSVG(color = '#CBDCE8', width = 21, height = 22) {
    return `
    <div class="custom-svg-wrapper"
         style="width:${width}px;height:${height}px;display:flex;align-items:center;justify-content:center;">
        <svg xmlns="http://www.w3.org/2000/svg"
             width="${width}"
             height="${height}"
             viewBox="0 0 21 22"
             fill="none">

            <!-- Main shape -->
            <path
                d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704261 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295Z"
                fill="${color}" />

            <!-- Inner details -->
            <path
                d="M16 6L11.5556 10.7222M9.58111 10.4917C8.20333 11.0206 7.10167 10.93 6 10.4933C6.27778 14.0728 7.94667 15.4489 10.1717 16C10.1717 16 11.8478 14.8144 12.0894 12.0039C12.1156 11.6994 12.1283 11.5478 12.0656 11.3761C12.0022 11.2044 11.8778 11.0817 11.6294 10.8356C11.2206 10.4311 11.0167 10.2289 10.7739 10.1778C10.5311 10.1278 10.2144 10.2489 9.58111 10.4917Z"
                fill="${color}" />

            <!-- Strokes -->
            <path
                d="M16 6L11.5556 10.7222M9.58111 10.4917C8.20333 11.0206 7.10167 10.93 6 10.4933C6.27778 14.0728 7.94667 15.4489 10.1717 16C10.1717 16 11.8478 14.8144 12.0894 12.0039"
                stroke="white"
                stroke-linecap="round"
                stroke-linejoin="round" />

            <path
                d="M6.83331 13.4703C6.83331 13.4703 8.2222 13.7392 9.61109 12.667"
                stroke="white"
                stroke-linecap="round"
                stroke-linejoin="round" />

            <path
                d="M9.05558 8.36144C9.05558 8.54561 8.98241 8.72225 8.85218 8.85248C8.72194 8.98272 8.54531 9.05588 8.36113 9.05588C8.17695 9.05588 8.00032 8.98272 7.87009 8.85248C7.73985 8.72225 7.66669 8.54561 7.66669 8.36144C7.66669 8.17726 7.73985 8.00062 7.87009 7.87039C8.00032 7.74016 8.17695 7.66699 8.36113 7.66699C8.54531 7.66699 8.72194 7.74016 8.85218 7.87039C8.98241 8.00062 9.05558 8.17726 9.05558 8.36144Z"
                fill="${color}"
                stroke="white" />

            <path
                d="M10.4445 6.55554V6.6111"
                stroke="white"
                stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    </div>`;
}


document.addEventListener('DOMContentLoaded', function () {
    // Initialize Leaflet map globally
    window.map = L.map('map', {
        zoomControl: true,
        attributionControl: false,
        preferCanvas: true
    });

    // Grey base map
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd', maxZoom: 20
    }).addTo(window.map);

    // Labels overlay
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd', maxZoom: 20, pane: 'overlayPane'
    }).addTo(window.map);

    // Initialize SECOND map for space view
    window.spaceMap = L.map('space-map', {
        zoomControl: true,
        attributionControl: false,
        preferCanvas: true
    });

    // Grey base map for space map
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd', maxZoom: 20
    }).addTo(window.spaceMap);

    // Labels overlay for space map
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png', {
        subdomains: 'abcd', maxZoom: 20, pane: 'overlayPane'
    }).addTo(window.spaceMap);

    // Yellow pin
    const yellowPin = L.icon({
        iconUrl: 'data:image/svg+xml;utf8,' + encodeURIComponent(`
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="48" viewBox="0 0 34 48" fill="none">
                <path d="M17 22.8C15.3898 22.8 13.8455 22.1679 12.7069 21.0426C11.5682 19.9174 10.9286 18.3913 10.9286 16.8C10.9286 15.2087 11.5682 13.6826 12.7069 12.5574C13.8455 11.4321 15.3898 10.8 17 10.8C18.6102 10.8 20.1545 11.4321 21.2931 12.5574C22.4318 13.6826 23.0714 15.2087 23.0714 16.8C23.0714 17.5879 22.9144 18.3681 22.6093 19.0961C22.3042 19.8241 21.8569 20.4855 21.2931 21.0426C20.7294 21.5998 20.0601 22.0417 19.3234 22.3433C18.5868 22.6448 17.7973 22.8 17 22.8ZM17 0C12.4913 0 8.1673 1.76999 4.97918 4.92061C1.79107 8.07122 0 12.3444 0 16.8C0 29.4 17 48 17 48C17 48 34 29.4 34 16.8C34 12.3444 32.2089 8.07122 29.0208 4.92061C25.8327 1.76999 21.5087 0 17 0Z" fill="#FFC97A"/>
            </svg>
        `),
        iconSize: [28, 28],
        iconAnchor: [14, 28],
        popupAnchor: [0, -26]
    });

    // groomerLocations for groomer map
    const groomerLocations = [
        {
            loc_name: "Sarah's Grooming Studio",
            name: "Sarah W.",
            lat: 51.5033,
            lng: -0.1147,
            image: BASE_URL + 'assets/images/card1.png', // use BASE_URL
            distance: '2.5 mi',
            rating: '4.3',
            reviews: '20'
        },
        {
            loc_name: "Westminster Pet Spa",
            name: "Sarah W.",
            lat: 51.4995,
            lng: -0.1248,
            image: BASE_URL + 'assets/images/card2.png',
            distance: '3.1 mi',
            rating: '4.7',
            reviews: '45'
        },
        {
            loc_name: "Strand Grooming",
            name: "Sarah W.",
            lat: 51.511227,
            lng: -0.119470,
            image: BASE_URL + 'assets/images/card3.png',
            distance: '1.8 mi',
            rating: '4.5',
            reviews: '32'
        }
    ];

    const spaceLocations = [
        {
            loc_name: 'Furs & Co. Studio',
            name: 'Dev É',
            lat: 51.5074,
            lng: -0.1657,
            image: BASE_URL + 'assets/images/space_card3.png',
            distance: '2.5 mi',
            rating: '4.3',
            reviews: '20'
        },
        {
            loc_name: 'Kensington Gardens',
            name: 'Kensington Gardens',
            lat: 51.5074,
            lng: -0.1850,
            image: BASE_URL + 'assets/images/space_card1.png',
            distance: '3.1 mi',
            rating: '4.7',
            reviews: '45'
        },
        {
            loc_name: "Regent's Park",
            name: "Regent's Park",
            lat: 51.5313,
            lng: -0.1568,
            image: BASE_URL + 'assets/images/space_card2.png',
            distance: '1.8 mi',
            rating: '4.5',
            reviews: '32'
        }
    ];


    // Small location SVG
    const locationSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14" viewBox="0 0 10 14" fill="none" style="vertical-align:middle; margin-right:2px;">
        <path d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z" fill="#FFC97A"/>
    </svg>
    `;

    // Small star SVG
    const starSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14" viewBox="0 0 14 14" fill="none" style="vertical-align:middle; margin-right:2px;">
        <path d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z" fill="#FFC97A"/>
    </svg>
    `;

    // Add markers to groomer map
    groomerLocations.forEach((loc, index) => {

        const imageUrl = loc.image || 'https://via.placeholder.com/300';
        const svgImage = groomerTooltipCardSVG(imageUrl, `groomer-clip-${index}`);

        const tooltipContent = `
        <div style="min-width:215px;position:relative;">
            <div class="map-top-left-svg">
                ${customTooltipSVG('#C9DDA0', 7, 8)}
            </div>
            <div style="display:flex; gap:2px;">
                ${svgImage}
                <div style="flex:1;">
                    <h2 class="name" style="margin:0 0 0px;font-size:14px;font-weight:600;color:#333;">
                        ${loc.loc_name}
                    </h2>
                    <span class="studio"> ${loc.name}</span>
                    <div class="map-meta d-flex align-items-center justify-content-between mt-2" style="font-size:12px;color:#666;line-height:1.4;">
                        <span class="d-flex align-items-center">${locationSVG} ${loc.distance || '2.5 mi'}</span>
                        <span class="d-flex align-items-center">${starSVG} ${loc.rating || '4.3'} (${loc.reviews || '20'})</span>
                    </div>
                </div>
            </div>
        </div>
        `;

        L.marker([loc.lat, loc.lng], { icon: yellowPin })
            .addTo(window.map)
            .bindPopup(tooltipContent, {
                closeButton: false, // remove the X
                autoClose: false,   // don't close other popups
                className: 'custom-popup',
                offset: [0, -35]
            })
            .on('mouseover', function (e) {
                this.openPopup();
            })
            .on('mouseout', function (e) {
                this.closePopup();
            });
    });




    // Add markers to space map
    spaceLocations.forEach((loc, index) => {

        const imageUrl = loc.image || 'https://via.placeholder.com/300';
        const svgImage = spaceTooltipCardSVG(imageUrl, `space-clip-${index}`);

        const tooltipContent = `
        <div style="min-width:215px;position:relative;">
            <div class="map-top-left-svg">
                ${spaceCustomTooltipSVG('#CBDCE8', 7, 8)}
            </div>
            <div style="display:flex; gap:2px;">
                ${svgImage}
                <div style="flex:1;">
                    <h2 class="name" style="margin:0 0 0px;font-size:14px;font-weight:600;color:#333;">
                        ${loc.loc_name}
                    </h2>
                    <h2 class="name" style="margin:0 0 0px;font-size:14px;font-weight:600;color:#333;">Hosted by <span class="studio"> ${loc.name}</span></h2>                
                    <div class="map-meta d-flex align-items-center justify-content-between mt-2" style="font-size:12px;color:#666;line-height:1.4;">
                        <span class="d-flex align-items-center">${locationSVG} ${loc.distance || '2.5 mi'}</span>
                        <span class="d-flex align-items-center">${starSVG} ${loc.rating || '4.3'} (${loc.reviews || '20'})</span>
                    </div>
                </div>
            </div>
        </div>
        `;

        L.marker([loc.lat, loc.lng], { icon: yellowPin })
            .addTo(window.spaceMap)
            .bindPopup(tooltipContent, {
                closeButton: false, // remove the X
                autoClose: false,   // don't close other popups
                className: 'custom-popup',
                offset: [0, -35]
            })
            .on('mouseover', function (e) {
                this.openPopup();
            })
            .on('mouseout', function (e) {
                this.closePopup();
            });
    });


    // ---- Custom Tabs ----
    document.querySelectorAll('.tabs').forEach(tabSection => {
        const buttons = tabSection.querySelectorAll('.tablinks');
        const contents = tabSection.querySelectorAll('.tabcontent');

        function activateTab(tabName) {
            contents.forEach(c => {
                const isActive = c.dataset.tabContent === tabName;
                c.style.display = isActive ? 'block' : 'none';

                // Handle groomer map tab
                if (isActive && tabName === 'groomer-map-view' && window.map) {
                    setTimeout(() => {
                        window.map.invalidateSize();
                        window.map.fitBounds(
                            groomerLocations.map(l => [l.lat, l.lng]),
                            {
                                padding: [40, 40],
                                maxZoom: 15
                            }
                        );
                    }, 100);
                }

                // Handle space map tab
                if (isActive && tabName === 'space-map-view' && window.spaceMap) {
                    setTimeout(() => {
                        window.spaceMap.invalidateSize();
                        window.spaceMap.fitBounds(
                            spaceLocations.map(l => [l.lat, l.lng]),
                            {
                                padding: [40, 40],
                                maxZoom: 15
                            }
                        );
                    }, 100);
                }
            });

            buttons.forEach(b => {
                b.classList.toggle('active', b.dataset.tab === tabName);
            });
        }

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                activateTab(button.dataset.tab);
            });
        });

        // Auto-activate first tab
        if (buttons.length) {
            activateTab(buttons[0].dataset.tab);
        }
    });
});

const toggleBtn = document.querySelector('.menu-toggle');
const menu = document.querySelector('.menu-items');
const header = document.querySelector('.logo-toggle-button');

toggleBtn.addEventListener('click', () => {
    menu.classList.toggle('active');
    header.classList.toggle('fixed');
    document.body.classList.toggle('menu-open');

    toggleBtn.innerHTML = menu.classList.contains('active') ? '✖' : '&#9776;';
});

// sort and venue selection starts

// loop through all venu-sorting-section blocks
document.querySelectorAll('.venu-sorting-section').forEach(container => {
    const sortBy = container.querySelector('.sort-by');
    const sortByFilter = container.querySelector('.sort-by-filter');

    const venueSelection = container.querySelector('.venue-selection');
    const venueList = container.querySelector('.venue-list');

    sortBy.addEventListener('click', () => {
        // hide venue dropdown only inside this container
        venueList.style.display = 'none';

        // toggle sort dropdown
        sortByFilter.style.display = (sortByFilter.style.display === 'block') ? 'none' : 'block';
    });

    venueSelection.addEventListener('click', () => {
        // hide sort dropdown only inside this container
        sortByFilter.style.display = 'none';

        // toggle venue dropdown
        venueList.style.display = (venueList.style.display === 'block') ? 'none' : 'block';
    });
});


// sort and venue selection ends


// selected filter remove js starts

document.querySelectorAll('.selected-item-section').forEach(section => {
    section.addEventListener('click', e => {
        if (e.target.classList.contains('cross')) {
            e.target.closest('.selected-item')?.remove();
        }
    });
});

// selected filter remove js ends


// main tabs content view starts

const main_tabs = document.querySelectorAll('.top-tabs');
const groomer_form_fields = document.querySelector('.find-groomer-search-content-area');
const space_form_fields = document.querySelector('.find-space-search-content-area');

main_tabs.forEach(button => {
    button.addEventListener('click', () => {
        const tabName = button.dataset.section;

        if (tabName === 'groomer') {
            groomer_form_fields.style.display = 'block';
            space_form_fields.style.display = 'none';
        } else {
            groomer_form_fields.style.display = 'none';
            space_form_fields.style.display = 'block';
        }

        // Hide all tab contents
        document.querySelectorAll('.main-tab-content').forEach(tc => {
            tc.style.display = 'none';
            tc.classList.remove('active');
        });

        // Remove active class from all buttons
        main_tabs.forEach(btn => btn.classList.remove('active'));

        // Show clicked tab content
        document.getElementById(tabName).style.display = 'block';

        // Set clicked button as active
        setTimeout(() => {
            button.classList.add('active');
        }, 10);
    });
});

// main tabs content view ends


// inner tabs content view starts

// const tabButtons = document.querySelectorAll('.tablinks');

// tabButtons.forEach(button => {
//     button.addEventListener('click', () => {
//         const tabName = button.dataset.tab;

//         // Hide all tab contents
//         document.querySelectorAll('.tabcontent').forEach(tc => tc.style.display = 'none');

//         // Remove active class from all buttons
//         tabButtons.forEach(btn => btn.classList.remove('active'));

//         // Show clicked tab content
//         document.getElementById(tabName).style.display = 'block';

//         // Set clicked button as active
//         button.classList.add('active');
//     });
// });


// loop through each .tabs section
// document.querySelectorAll('.tabs').forEach(tabSection => {
//     const tabButtons = tabSection.querySelectorAll('.tablinks');
//     const tabContents = tabSection.querySelectorAll('.tabcontent');

//     tabButtons.forEach(button => {
//         button.addEventListener('click', () => {
//             const tabName = button.dataset.tab;

//             // Hide all tab contents in this section only
//             tabContents.forEach(tc => tc.style.display = 'none');

//             // Remove active class from all buttons in this section
//             tabButtons.forEach(btn => btn.classList.remove('active'));

//             // Show clicked tab content
//             const content = tabSection.querySelector(`#${tabName}`);
//             if (content) content.style.display = 'block';

//             // Set clicked button as active
//             button.classList.add('active');
//         });
//     });
// });

// dynamic tabs 
document.querySelectorAll('.tabs').forEach(tabSection => {
    const buttons = tabSection.querySelectorAll('.tablinks');
    const contents = tabSection.querySelectorAll('.tabcontent');

    function activateTab(tabName) {
        contents.forEach(c => {
            const isActive = c.dataset.tabContent === tabName;
            c.style.display = isActive ? 'block' : 'none';

            // If this is the map tab, force Leaflet to recalc size
            if (isActive && tabName === 'groomer-map-view' && window.map) {
                setTimeout(() => {
                    map.invalidateSize(); // map is your Leaflet map instance
                }, 100); // small delay to let layout finish
            }
        });

        buttons.forEach(b => {
            b.classList.toggle('active', b.dataset.tab === tabName);
        });
    }

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            activateTab(button.dataset.tab);
        });
    });

    // Auto-activate first tab
    if (buttons.length) {
        activateTab(buttons[0].dataset.tab);
    }
});




// inner tabs content view ends



// venu and sort by JS to handle selection
document.querySelectorAll('.dropdown').forEach(dropdown => {
    const items = dropdown.querySelectorAll('ul li');

    items.forEach(item => {
        item.addEventListener('click', () => {
            items.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });
    });
});

// tabs content view ends

// custom select dropdown js  

document.querySelectorAll('.service-type-select .custom-select').forEach(select => {
    const trigger = select.querySelector('.service-type-select .custom-select .select-trigger');
    const options = select.querySelectorAll('.service-type-select .custom-select .select-options li');
    const datePopovers = document.querySelectorAll('.popover');
    const text = select.querySelector('.selected-text');
    const hiddenInput = select.querySelector('input[type="hidden"]');

    trigger.addEventListener('click', e => {
        e.stopPropagation();

        datePopovers.forEach(popover => {
            popover.style.display = 'none';
        });

        document.querySelectorAll('.custom-select').forEach(s => {
            if (s !== select) {
                s.classList.remove('open');
                const t = s.querySelector('.select-trigger');
                t.style.cssText = `
                    border-bottom-left-radius: 12px;
                    border-bottom-right-radius: 12px;
                `;
            }
        });

        const isOpen = select.classList.toggle('open');

        trigger.style.cssText = isOpen
            ? `border-bottom-left-radius: 0; border-bottom-right-radius: 0;`
            : `border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;`;
    });

    options.forEach(option => {
        option.addEventListener('click', () => {
            text.textContent = option.textContent;
            hiddenInput.value = option.dataset.value;

            select.classList.remove('open');
            select.classList.add('has-value'); // add class to highlight border

            trigger.style.cssText = `
                border-bottom-left-radius: 12px;
                border-bottom-right-radius: 12px;
            `;
        });
    });
});

// date time picker js  

(function () {
    const datetimeWrappers = document.querySelectorAll('.datetime-wrapper');

    datetimeWrappers.forEach((wrapper) => {
        const dateField = wrapper.querySelector('.field.date');
        const dateInput = dateField.querySelector('.fake-input');
        const datePopover = dateField.querySelector('.popover');
        const daysGrid = datePopover.querySelector('.days-grid');
        const monthLabel = datePopover.querySelector('#monthLabel');
        const prevMonthBtn = datePopover.querySelector('#prevMonth');
        const nextMonthBtn = datePopover.querySelector('#nextMonth');
        const weekdayRow = datePopover.querySelector('.weekday-row');

        const timeField = wrapper.querySelector('.field.time');
        const timeInput = timeField.querySelector('.fake-input');
        const timeList = datePopover.querySelector('.time-list');

        const weekdays = ['M', 'T', 'W', 'T', 'F', 'S', 'S'];

        let selectedDate = new Date();
        let viewYear = selectedDate.getFullYear();
        let viewMonth = selectedDate.getMonth();
        let selectedTime = '13:00'; // internal 24h value

        /* ---------------------- Utility ---------------------- */
        function pad(n) { return n < 10 ? '0' + n : n; }
        function monthName(m) { return new Date(2000, m, 1).toLocaleString('en', { month: 'long' }); }
        function formatDateForInput(d) {
            const day = pad(d.getDate());
            const month = pad(d.getMonth() + 1);
            const year = d.getFullYear().toString().slice(-2);
            return `${day}/${month}/${year}`;
        }

        function isSameDate(a, b) { return a && b && a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate(); }
        function isToday(d) { const t = new Date(); return isSameDate(d, t); }

        // AM / PM helper (KEEP 24h numbers)
        function withMeridiem(hour) {
            return hour < 12 ? 'AM' : 'PM';
        }

        /* ---------------------- Calendar ---------------------- */
        function renderWeekdays() {
            weekdayRow.innerHTML = '';
            weekdays.forEach(d => {
                const el = document.createElement('div');
                el.textContent = d;
                weekdayRow.appendChild(el);
            });
        }

        function renderCalendar(year, month) {
            monthLabel.textContent = `${monthName(month)} ${year}`;
            daysGrid.innerHTML = '';

            const firstDay = new Date(year, month, 1);
            const startOffset = (firstDay.getDay() + 6) % 7;
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const prevMonthLastDay = new Date(year, month, 0).getDate();
            const totalCells = Math.ceil((startOffset + daysInMonth) / 7) * 7;

            for (let i = 0; i < totalCells; i++) {
                const cell = document.createElement('div');
                cell.className = 'day';
                const dayIndex = i - startOffset + 1;

                if (i < startOffset) {
                    cell.textContent = prevMonthLastDay - (startOffset - 1 - i);
                    cell.classList.add('outside');
                } else if (dayIndex > daysInMonth) {
                    cell.textContent = dayIndex - daysInMonth;
                    cell.classList.add('outside');
                } else {
                    const cellDate = new Date(year, month, dayIndex);
                    cell.textContent = dayIndex;

                    if (isSameDate(cellDate, selectedDate)) cell.classList.add('selected');
                    else if (isToday(cellDate)) cell.classList.add('today');

                    cell.tabIndex = 0;
                    cell.addEventListener('click', () => {
                        selectedDate = cellDate;
                        dateInput.value = formatDateForInput(selectedDate);
                        dateField.classList.add('has-value');
                        closeAllPopovers();
                        renderCalendar(viewYear, viewMonth);
                    });
                }

                daysGrid.appendChild(cell);
            }
        }

        /* ---------------------- Time List ---------------------- */
        function generateTimes() {
            timeList.innerHTML = '';

            for (let hour = 0; hour < 24; hour++) {
                const timeValue = pad(hour) + ':00';
                const item = document.createElement('div');

                item.className = 'time-item';
                item.dataset.time = timeValue;
                item.tabIndex = 0;

                // DISPLAY: 24h + AM/PM
                item.textContent = `${timeValue} ${withMeridiem(hour)}`;

                item.addEventListener('click', () => {
                    selectedTime = timeValue;
                    timeInput.value = `${timeValue} ${withMeridiem(hour)}`;
                    timeField.classList.add('has-value');

                    timeList.querySelectorAll('.time-item')
                        .forEach(i => i.classList.remove('selected'));

                    item.classList.add('selected');
                    closeAllPopovers();
                });

                timeList.appendChild(item);
            }
        }

        /* ---------------------- Popover ---------------------- */
        function openPopover() {
            datePopover.style.display = 'block';
            dateField.classList.add('focused');
            timeField.classList.add('focused');

            dateField.style.borderBottomLeftRadius = '0px';
            timeField.style.borderBottomRightRadius = '0px';

            // Auto scroll to selected time
            const el = timeList.querySelector(`.time-item[data-time="${selectedTime}"]`);
            if (el) el.scrollIntoView({ block: 'center', behavior: 'smooth' });
        }

        function closeAllPopovers() {
            document.querySelectorAll('.popover').forEach(p => p.style.display = 'none');

            dateField.classList.remove('focused');
            timeField.classList.remove('focused');

            dateField.style.borderBottomLeftRadius = '10px';
            timeField.style.borderBottomRightRadius = '10px';
        }

        /* ---------------------- Events ---------------------- */
        dateField.querySelector('.input-row').addEventListener('click', () => {
            datePopover.style.display === 'block' ? closeAllPopovers() : openPopover();
        });

        timeField.querySelector('.input-row').addEventListener('click', () => {
            if (datePopover.style.display !== 'block') openPopover();
            else {
                const el = timeList.querySelector(`.time-item[data-time="${selectedTime}"]`);
                if (el) el.scrollIntoView({ block: 'center', behavior: 'smooth' });
            }
        });

        prevMonthBtn.addEventListener('click', () => {
            viewMonth--;
            if (viewMonth < 0) { viewMonth = 11; viewYear--; }
            renderCalendar(viewYear, viewMonth);
        });

        nextMonthBtn.addEventListener('click', () => {
            viewMonth++;
            if (viewMonth > 11) { viewMonth = 0; viewYear++; }
            renderCalendar(viewYear, viewMonth);
        });

        /* ---------------------- Init ---------------------- */
        function init() {
            renderWeekdays();
            renderCalendar(viewYear, viewMonth);
            generateTimes();

            dateInput.value = formatDateForInput(selectedDate);
            timeInput.value = `${selectedTime} ${withMeridiem(parseInt(selectedTime))}`;

            const selectedEl = timeList.querySelector(`.time-item[data-time="${selectedTime}"]`);
            if (selectedEl) {
                selectedEl.classList.add('selected');
                selectedEl.scrollIntoView({ block: 'center' });
            }
        }

        init();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.datetime-wrapper')) {
            document.querySelectorAll('.popover').forEach(p => p.style.display = 'none');
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.popover').forEach(p => p.style.display = 'none');
        }
    });
})();


// calendar js simple
/* ---------- STATE ---------- */
// let selectedDate = new Date();
// const weekEl = document.getElementById('week');
// const rangeEl = document.getElementById('range');
// const monthEl = document.getElementById('month');

// /* ---------- HELPERS ---------- */
// const addDays = (d, n) => new Date(d.getFullYear(), d.getMonth(), d.getDate() + n);
// const monday = d => addDays(d, -((d.getDay() + 6) % 7));
// const pad = n => String(n).padStart(2, '0');

// /* ---------- RENDER ---------- */
// function render() {
//     weekEl.innerHTML = '';

//     const start = monday(selectedDate);
//     const end = addDays(start, 6);

//     rangeEl.textContent = `${pad(start.getDate())} - ${pad(end.getDate())} ${start.toLocaleString(undefined, { month: 'long' })}`;
//     monthEl.textContent = selectedDate.toLocaleString(undefined, { month: 'long', year: 'numeric' });

//     for (let i = 0; i < 7; i++) {
//         const d = addDays(start, i);
//         const day = document.createElement('div');
//         day.className = 'week-days';

//         // add inner HTML
//         day.innerHTML = `
//             <div class="dow${d.toDateString() === selectedDate.toDateString() ? ' active' : ''}">
//             ${d.toLocaleString(undefined, { weekday: 'short' })}
//             </div>
//             <div class="date${d.toDateString() === selectedDate.toDateString() ? ' active' : ''}">
//             ${pad(d.getDate())}
//             </div>
//         `;

//         day.onclick = () => { selectedDate = d; render(); };
//         weekEl.appendChild(day);
//     }
// }

// /* ---------- WEEK NAV ---------- */
// prev.onclick = () => { selectedDate = addDays(selectedDate, -7); render(); }
// next.onclick = () => { selectedDate = addDays(selectedDate, 7); render(); }

// /* ---------- INIT ---------- */
// render();


// draggable js 

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.scroll-calendar').forEach(calendar => {
        // ---------- Elements ----------
        const weekContainer = calendar.querySelector('.week-container');
        const weekEl = calendar.querySelector('.week');
        const rangeEl = calendar.querySelector('.range');
        const monthEl = calendar.querySelector('.month');
        const prev = calendar.querySelector('.prev');
        const next = calendar.querySelector('.next');
        const centerIndicator = calendar.querySelector('.center-indicator');

        // ---------- State ----------
        let selectedDate = new Date();
        let isDragging = false;
        let startX = 0;
        let currentTranslate = 0;
        let previousTranslate = 0;
        let velocityX = 0;
        let lastMoveTime = 0;
        let lastMoveX = 0;
        let hasMoved = false;

        // How many tiles to render (odd number so selected is exactly centered)
        const RENDER_COUNT = 21; // 10 days on each side; changeable
        const CENTER_INDEX = Math.floor(RENDER_COUNT / 2);

        // ---------- Helper Functions ----------
        const addDays = (d, n) => new Date(d.getFullYear(), d.getMonth(), d.getDate() + n);
        const monday = d => addDays(d, -((d.getDay() + 6) % 7));
        const pad = n => String(n).padStart(2, '0');

        function getDayWidth() {
            const children = Array.from(weekEl.children);
            if (children.length >= 2) {
                const r0 = children[0].getBoundingClientRect();
                const r1 = children[1].getBoundingClientRect();
                return r1.left - r0.left;
            }
            // fallback estimate based on container
            return (weekContainer.clientWidth / 7) || 100;
        }

        function computeCenterTranslate(dayWidth) {
            // translate so that the center tile is positioned at container center
            const containerWidth = weekContainer.clientWidth;
            const centerTranslate = -dayWidth * CENTER_INDEX + (containerWidth - dayWidth) / 2;
            return centerTranslate;
        }

        function render(skipTransition = false) {
            if (skipTransition) weekEl.classList.add('no-transition');
            else weekEl.classList.remove('no-transition');

            weekEl.innerHTML = '';

            // centerDate is the selected date
            const startDate = addDays(selectedDate, -CENTER_INDEX);

            // Update range: show the 7 visible days centered around selectedDate
            // (3 days before → selected → 3 days after = 7 tiles visible)
            const visibleStart = addDays(selectedDate, -3);
            const visibleEnd   = addDays(selectedDate,  3);
            const startMonth   = visibleStart.toLocaleString(undefined, { month: 'long' });
            const endMonth     = visibleEnd.toLocaleString(undefined,   { month: 'long' });
            const rangeMonth   = startMonth === endMonth
                ? startMonth
                : `${startMonth} / ${endMonth}`;
            rangeEl.textContent = `${pad(visibleStart.getDate())} - ${pad(visibleEnd.getDate())} ${rangeMonth}`;
            monthEl.textContent = selectedDate.toLocaleString(undefined, { month: 'long', year: 'numeric' });

            // Render RENDER_COUNT days centered around selectedDate
            for (let i = 0; i < RENDER_COUNT; i++) {
                const d = addDays(startDate, i);
                const isActive = d.toDateString() === selectedDate.toDateString();
                const day = document.createElement('div');
                day.className = 'week-days';
                day.innerHTML = `
                    <div class="dow${isActive ? ' active' : ''}">
                        ${d.toLocaleString(undefined, { weekday: 'short' })}
                    </div>
                    <div class="date${isActive ? ' active' : ''}">
                        ${pad(d.getDate())}
                    </div>
                `;
                day.onclick = (e) => {
                    // only select if it wasn't a drag motion
                    if (!hasMoved) {
                        selectedDate = d;
                        render(false);
                    }
                };
                weekEl.appendChild(day);
            }

            // Force layout to measure
            requestAnimationFrame(() => {
                const dayWidth = getDayWidth();
                // compute translate that centers the middle tile
                const centerTranslate = computeCenterTranslate(dayWidth);

                // set transforms
                previousTranslate = centerTranslate;
                currentTranslate = centerTranslate;
                setTranslate(centerTranslate);

                // remove no-transition after a short delay if skipTransition was true
                if (skipTransition) {
                    setTimeout(() => weekEl.classList.remove('no-transition'), 20);
                }
            });
        }

        function setTranslate(position) {
            weekEl.style.transform = `translateX(${position}px)`;
        }

        // ---------- Drag handling ----------
        function dragStart(e) {
            isDragging = true;
            hasMoved = false;
            weekContainer.classList.add('dragging');

            startX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            velocityX = 0;
            lastMoveTime = Date.now();
            lastMoveX = startX;

            weekEl.classList.add('no-transition');
        }

        function drag(e) {
            if (!isDragging) return;
            e.preventDefault();

            const currentX = e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
            const diff = currentX - startX;

            if (Math.abs(diff) > 5) hasMoved = true;

            currentTranslate = previousTranslate + diff;

            // velocity
            const now = Date.now();
            const dt = now - lastMoveTime;
            if (dt > 0) {
                velocityX = (currentX - lastMoveX) / dt;
                lastMoveTime = now;
                lastMoveX = currentX;
            }

            setTranslate(currentTranslate);
        }

        function dragEnd() {
            if (!isDragging) return;
            isDragging = false;
            weekContainer.classList.remove('dragging');
            weekEl.classList.remove('no-transition');

            const dayWidth = getDayWidth();

            // apply momentum (scale velocity for nicer feel)
            const momentum = velocityX * 200; // tweak scalar if desired
            let finalTranslate = currentTranslate + momentum;

            // compute how many whole day-widths moved relative to previousTranslate
            const movedDays = Math.round((previousTranslate - finalTranslate) / dayWidth);

            if (movedDays !== 0) {
                selectedDate = addDays(selectedDate, movedDays);
                // render will compute new center and animate into place
                render(false);
            } else {
                // snap back to center (no date change)
                setTranslate(previousTranslate);
            }

            // reset velocities
            velocityX = 0;
            hasMoved = false;
        }

        // ---------- Event listeners ----------
        // Mouse
        weekContainer.addEventListener('mousedown', dragStart);
        window.addEventListener('mousemove', drag);
        window.addEventListener('mouseup', dragEnd);

        // Touch
        weekContainer.addEventListener('touchstart', dragStart, { passive: true });
        weekContainer.addEventListener('touchmove', drag, { passive: false });
        weekContainer.addEventListener('touchend', dragEnd);

        // Buttons: move one day left/right
        if (prev) prev.onclick = () => {
            selectedDate = addDays(selectedDate, -1);
            render(false);
        };
        if (next) next.onclick = () => {
            selectedDate = addDays(selectedDate, 1);
            render(false);
        };

        // keyboard navigation (container needs tabindex)
        weekContainer.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                selectedDate = addDays(selectedDate, -1);
                render(false);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                selectedDate = addDays(selectedDate, 1);
                render(false);
            }
        });

        // handle resize so center remains accurate
        window.addEventListener('resize', () => {
            // recompute center translate after resize
            requestAnimationFrame(() => {
                const dayWidth = getDayWidth();
                previousTranslate = computeCenterTranslate(dayWidth);
                setTranslate(previousTranslate);
            });
        });

        // ---------- Initialize ----------
        render();
    });
});


// modal js starts 

// Open modal
document.addEventListener('click', e => {
    const openBtn = e.target.closest('[data-modal-open]');
    if (!openBtn) return;

    const modalId = openBtn.dataset.modalOpen;
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = 'flex';
});

// Close modal (close button or backdrop)
document.addEventListener('click', e => {
    // Close button
    if (e.target.classList.contains('modal-close')) {
        e.target.closest('.modal').style.display = 'none';
    }

    // Backdrop click
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});

// modal js ends 


// range js starts 

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.range-slider').forEach(slider => {
        const range = slider.querySelector('input[type="range"]');
        const output = slider.querySelector('.output');
        const inclRange = slider.querySelector('.incl-range');
        const max = range.max;

        function updateView() {
            const value = range.value;
            const percent = (value / max) * 100;

            output.textContent = '£' + value;
            output.style.left = percent + '%';
            inclRange.style.width = percent + '%';
        }

        updateView();
        range.addEventListener('input', updateView);
    });

});

// range js ends  