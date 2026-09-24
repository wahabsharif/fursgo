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
//         { name: 'Sarah', lat: 51.511227, lng: -0.119470 }
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
         width="41"
         height="60"
         viewBox="0 0 41 60"
         style="display:block;">
        <defs>
            <clipPath id="${clipId}">
                <path d="M41 58C41 59.1046 40.1046 60 39 60H2C0.895431 60 0 59.1046 0 58V14C0 12.8954 0.895431 12 2 12H10C11.1046 12 12 11.1046 12 10V2C12 0.895431 12.8954 0 14 0H39C40.1046 0 41 0.895431 41 2V58Z"/>
            </clipPath>
        </defs>

        <image
            href="${imageUrl}"
            width="41"
            height="60"
            preserveAspectRatio="xMidYMid slice"
            clip-path="url(#${clipId})" />
    </svg>`;
}

function spaceTooltipCardSVG(imageUrl, clipId) {
    return `
    <svg xmlns="http://www.w3.org/2000/svg"
         width="41"
         height="60"
         viewBox="0 0 41 60"
         style="display:block;">
        <defs>
            <clipPath id="${clipId}">
                <path d="M41 58C41 59.1046 40.1046 60 39 60H2C0.895431 60 0 59.1046 0 58V14C0 12.8954 0.895431 12 2 12H10C11.1046 12 12 11.1046 12 10V2C12 0.895431 12.8954 0 14 0H39C40.1046 0 41 0.895431 41 2V58Z"/>
            </clipPath>
        </defs>

        <image
            href="${imageUrl}"
            width="41"
            height="60"
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
    function getCartoTileUrl(style) {
        const key = window.CARTO_API_KEY || '';
        const base = 'https://{s}.basemaps.cartocdn.com/' + style + '/{z}/{x}/{y}{r}.png';
        return key ? (base + '?key=' + encodeURIComponent(key)) : base;
    }

    function getCartoTileOptions(extra) {
        return Object.assign({
            subdomains: 'abcd',
            maxZoom: 20,
            attribution: '&copy; <a href="https://carto.com/">CARTO</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }, extra || {});
    }

    const mapOptions = {
        zoomControl: false,
        attributionControl: false,
        preferCanvas: true,
        zoomAnimation: true,
        fadeAnimation: true,
        markerZoomAnimation: true,
        zoomSnap: 1,
        zoomDelta: 1,
        wheelPxPerZoomLevel: 90
    };

    // Initialize Leaflet map globally (only when containers exist)
    const mapEl = document.getElementById('map');
    const spaceMapEl = document.getElementById('space-map');

    if (mapEl && typeof L !== 'undefined') {
    window.map = L.map('map', { ...mapOptions });
    L.control.zoom({
        position: 'bottomright',
        zoomInTitle: 'Zoom in',
        zoomOutTitle: 'Zoom out'
    }).addTo(window.map);

    enableCtrlScrollZoom(window.map);

    // Grey base map
    L.tileLayer(getCartoTileUrl('light_nolabels'), getCartoTileOptions()).addTo(window.map);

    // Labels overlay
    L.tileLayer(getCartoTileUrl('light_only_labels'), getCartoTileOptions({
        pane: 'overlayPane'
    })).addTo(window.map);
    }

    // Initialize SECOND map for space view
    if (spaceMapEl && typeof L !== 'undefined') {
    window.spaceMap = L.map('space-map', { ...mapOptions });
    L.control.zoom({
        position: 'bottomright',
        zoomInTitle: 'Zoom in',
        zoomOutTitle: 'Zoom out'
    }).addTo(window.spaceMap);

    enableCtrlScrollZoom(window.spaceMap);

    // Grey base map for space map
    L.tileLayer(getCartoTileUrl('light_nolabels'), getCartoTileOptions()).addTo(window.spaceMap);

    // Labels overlay for space map
    L.tileLayer(getCartoTileUrl('light_only_labels'), getCartoTileOptions({
        pane: 'overlayPane'
    })).addTo(window.spaceMap);
    }

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
            image: (window.BASE_URL || '/') + 'images/card1.png', // use BASE_URL
            distance: '2.5 mi',
            rating: '4.3',
            reviews: '20'
        },
        {
            loc_name: "Westminster Pet Spa",
            name: "Sarah W.",
            lat: 51.4995,
            lng: -0.1248,
            image: (window.BASE_URL || '/') + 'images/card2.png',
            distance: '3.1 mi',
            rating: '4.7',
            reviews: '45'
        },
        {
            loc_name: "Sarah Grooming",
            name: "Sarah W.",
            lat: 51.511227,
            lng: -0.119470,
            image: (window.BASE_URL || '/') + 'images/card3.png',
            distance: '1.8 mi',
            rating: '4.5',
            reviews: '32'
        }
    ];

    const spaceLocations = [
        {
            loc_name: 'Furs & Co. Studio',
            name: 'Dev É.',
            lat: 51.5074,
            lng: -0.1657,
            image: (window.BASE_URL || '/') + 'images/space_card3.png',
            distance: '1.0 mi',
            rating: '4.3',
            reviews: '20'
        },
        {
            loc_name: 'Paws & Bubbles',
            name: 'Dev É.',
            lat: 51.5074,
            lng: -0.1850,
            image: (window.BASE_URL || '/') + 'images/space_card1.png',
            distance: '1.0 mi',
            rating: '4.3',
            reviews: '20'
        },
        {
            loc_name: 'The Garden Grooming Spot',
            name: 'Dev É.',
            lat: 51.5313,
            lng: -0.1568,
            image: (window.BASE_URL || '/') + 'images/space_card2.png',
            distance: '1.0 mi',
            rating: '4.3',
            reviews: '20'
        },
        {
            loc_name: 'Furs & Co. Studio',
            name: 'Dev É.',
            lat: 51.5155,
            lng: -0.1420,
            image: (window.BASE_URL || '/') + 'images/space_card3.png',
            distance: '1.0 mi',
            rating: '4.3',
            reviews: '20'
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
    if (window.map) {
    groomerLocations.forEach((loc, index) => {

        const imageUrl = loc.image || 'https://via.placeholder.com/300';
        const svgImage = groomerTooltipCardSVG(imageUrl, `groomer-clip-${index}`);

        const tooltipContent = `
        <div style="min-width:215px;position:relative;">
            <div class="map-top-left-svg">
                ${customTooltipSVG('#C9DDA0', 8, 11)}
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <div style="display:flex; justify-content:center; align-items:center;">
                    ${svgImage}
                </div>
                <div style="flex:1;">
                    <h2 class="name" style="margin:0 0 0px;font-size:14px;font-weight:600;color:#3B3731;">
                        ${loc.loc_name}
                    </h2>
                    <span class="studio"> ${loc.name}</span>
                    <div class="map-meta d-flex align-items-center justify-content-between mt-2" style="font-size:14px;color:#3B3731;font-weight: 500;line-height:1.4;">
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
            .on('mouseclick', function (e) {
                this.closePopup();
            });
    });
    }




    // Add markers to space map
    if (window.spaceMap) {
    spaceLocations.forEach((loc, index) => {

        const imageUrl = loc.image || 'https://via.placeholder.com/300';
        const svgImage = spaceTooltipCardSVG(imageUrl, `space-clip-${index}`);

        const tooltipContent = `
        <div style="min-width:215px;position:relative;">
            <div class="map-top-left-svg">
                ${spaceCustomTooltipSVG('#CBDCE8', 8, 11)}
            </div>
            <div style="display:flex; gap:10px; align-items:center;">
                <div style="display:flex; justify-content:center; align-items:center;">
                    ${svgImage}
                </div>
                <div style="flex:1;">
                    <h2 class="name" style="margin:0 0 0px;font-size:14px;font-weight:600;color:#3B3731;">
                        ${loc.loc_name}
                    </h2>
                    <h2 class="name" style="margin:0 0 0px;font-size:14px;font-weight:600;color:#3B3731;">Hosted by <span class="studio"> ${loc.name}</span></h2>                
                    <div class="map-meta d-flex align-items-center justify-content-between mt-2" style="font-size:14px;color:#3B3731;font-weight: 500;line-height:1.4;">
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
            .on('mouseclick', function (e) {
                this.closePopup();
            });
    });
    }

    function enableCtrlScrollZoom(map) {
        map.scrollWheelZoom.disable();

        const container = map.getContainer();
        const wrapper = container.closest('.map-wrapper') || container.parentElement;

        if (wrapper && !wrapper.querySelector('.map-zoom-hint')) {
            const hint = document.createElement('div');
            hint.className = 'map-zoom-hint';
            hint.setAttribute('aria-hidden', 'true');
            const isMac = /Mac|iPhone|iPad|iPod/.test(navigator.platform || '')
                || /Mac OS/.test(navigator.userAgent || '');
            hint.innerHTML = isMac
                ? '<span class="map-zoom-hint__key">âŒ˜</span> + scroll to zoom'
                : '<span class="map-zoom-hint__key">Ctrl</span> + scroll to zoom';
            wrapper.appendChild(hint);
        }

        const hint = wrapper && wrapper.querySelector('.map-zoom-hint');

        const syncScrollZoom = (e) => {
            if (e.ctrlKey || e.metaKey) {
                map.scrollWheelZoom.enable();
            } else {
                map.scrollWheelZoom.disable();
            }
        };

        // Enable map zoom as soon as Ctrl/âŒ˜ is held so the first wheel tick works
        document.addEventListener('keydown', syncScrollZoom);
        document.addEventListener('keyup', syncScrollZoom);
        window.addEventListener('blur', () => map.scrollWheelZoom.disable());

        container.addEventListener('wheel', function (e) {
            if (e.ctrlKey || e.metaKey) {
                // Stop the browser from zooming the page; only the map should zoom
                e.preventDefault();
                map.scrollWheelZoom.enable();

                if (hint) {
                    hint.classList.remove('is-prompt');
                }
            } else {
                map.scrollWheelZoom.disable();

                // Prompt when scrolling over the map without Ctrl (Google Maps-style)
                if (hint) {
                    hint.classList.add('is-prompt');
                    clearTimeout(map._hintTimeout);
                    map._hintTimeout = setTimeout(() => {
                        hint.classList.remove('is-prompt');
                    }, 1500);
                }
            }
        }, { passive: false });
    }


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

            // Refresh groomer/space result count for the visible view
            var main = tabSection.closest('.main-tab-content');
            if (main && main.id === 'groomer' && typeof filterByVenue === 'function') {
                filterByVenue('groomer-venue[]');
            }
            if (main && main.id === 'space' && typeof filterByVenue === 'function') {
                filterByVenue('space-venue[]');
            }
        }

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                activateTab(button.dataset.tab);
            });
        });

        // Prefer the tab already marked active in HTML (list/map/calendar focus from URL)
        if (buttons.length) {
            const preset = tabSection.querySelector('.tablinks.active');
            activateTab((preset && preset.dataset.tab) || buttons[0].dataset.tab);
        }
    });
});

const toggleBtn = document.querySelector('.menu-toggle');
const menu = document.querySelector('.menu-items');
const header = document.querySelector('.logo-toggle-button');

if (toggleBtn && menu && header && toggleBtn.dataset.cjMenuBound !== '1') {
    toggleBtn.dataset.cjMenuBound = '1';
    toggleBtn.addEventListener('click', () => {
        menu.classList.toggle('active');
        header.classList.toggle('fixed');
        document.body.classList.toggle('menu-open');
        toggleBtn.innerHTML = menu.classList.contains('active') ? '✖' : '&#9776;';
    });
}

// =========================================================
// SIMPLE FILTER + SORT (easy to read)
//
// Important HTML ids / classes used here:
//   document.getElementById('groomer')              -> groomer results area
//   document.getElementById('space')                -> space results area
//   document.getElementById('groomerSelectedSection')-> left pills for groomer
//   document.getElementById('spaceSelectedSection') -> left pills for space
//   document.querySelector('.sort-by')              -> Sort button
//   document.querySelector('.sort-by-filter')       -> Sort dropdown menu
//   document.querySelector('.venue-selection')      -> Venue button
//   document.querySelector('.venue-list')           -> Venue dropdown menu
//   document.querySelectorAll('.card')              -> result cards
//   document.querySelectorAll('.selected-item')     -> pills on the left
// =========================================================

function closeAllVenueSortDropdowns() {
    var menus = document.querySelectorAll('.sort-by-filter, .venue-list');
    var i;
    for (i = 0; i < menus.length; i++) {
        menus[i].style.display = 'none';
    }
}

// Which big results area? #groomer or #space
function getResultsArea(inputName) {
    if (inputName.indexOf('groomer') === 0) {
        return document.getElementById('groomer');
    }
    if (inputName.indexOf('space') === 0) {
        return document.getElementById('space');
    }
    return null;
}

// Which left pill box?
function getPillBox(inputName) {
    if (inputName.indexOf('groomer') === 0) {
        return document.getElementById('groomerSelectedSection');
    }
    if (inputName.indexOf('space') === 0) {
        return document.getElementById('spaceSelectedSection');
    }
    return null;
}

// Text next to checkbox/radio, example: "Salons" or "Lowest price"
function getOptionLabel(input) {
    var label = input.closest('label');
    if (!label) return input.value;

    var optionText = label.querySelector('.option-text');
    if (!optionText) return input.value;

    var copy = optionText.cloneNode(true);
    var tooltips = copy.querySelectorAll('.tooltip');
    var t;
    for (t = 0; t < tooltips.length; t++) {
        tooltips[t].remove();
    }
    return copy.textContent.replace(/\s+/g, ' ').trim() || input.value;
}

function getCrossIcon() {
    var cross = document.querySelector('.selected-item .cross');
    if (cross) {
        return cross.getAttribute('src');
    }
    return (window.BASE_URL || '/') + 'icons/cross.svg';
}

// Card sits inside Bootstrap column (.col-lg-3 or .col-lg-6)
// We hide/move the COLUMN so no empty hole stays in the row
function getCardColumn(card) {
    var col = card.closest('.col-lg-3, .col-lg-4, .col-lg-6');

    // Map column, or unknown -> hide the card itself
    if (!col || col.classList.contains('map-col')) {
        return card;
    }

    // If many cards share one column (map list), hide only this card
    var cardsInCol = col.querySelectorAll('.card');
    if (cardsInCol.length > 1) {
        return card;
    }

    return col;
}

// Only the open view: Calendar / Map / List
function getActivePanel(area) {
    if (!area) return null;

    var panels = area.querySelectorAll('.tabcontent');
    var i;
    for (i = 0; i < panels.length; i++) {
        // active panel is the one NOT hidden
        if (panels[i].style.display !== 'none') {
            return panels[i];
        }
    }

    // fallback: first panel
    if (panels.length) return panels[0];
    return area;
}

function getResultCards(root) {
    var list = [];
    if (!root) return list;

    var cards = root.querySelectorAll('.card');
    var i;
    for (i = 0; i < cards.length; i++) {
        var card = cards[i];
        // skip map popup / map-col only cards when not needed
        if (card.closest('.map-col')) continue;
        // selected unavailable lead card is not a result row
        if (card.classList.contains('specific')) continue;
        // must have a price
        if (!card.querySelector('.price')) continue;
        list.push(card);
    }
    return list;
}

// After hide/show: put visible columns first (no empty holes)
function packVisibleColumns(panel) {
    if (!panel) return;

    var rows = panel.querySelectorAll('.row');
    var r;
    for (r = 0; r < rows.length; r++) {
        var row = rows[r];
        var cols = row.querySelectorAll(':scope > .col-lg-3, :scope > .col-lg-4, :scope > .col-lg-6');
        if (!cols.length) continue;

        var loadMore = row.querySelector(':scope > .col-lg-12');
        var mapCol = row.querySelector(':scope > .map-col');
        var visible = [];
        var hidden = [];
        var i;

        for (i = 0; i < cols.length; i++) {
            if (cols[i].classList.contains('map-col')) continue;
            if (cols[i].style.display === 'none') {
                hidden.push(cols[i]);
            } else {
                visible.push(cols[i]);
            }
        }

        // Anchor before load-more / map so the map stays on the right
        var insertBefore = loadMore || mapCol || null;

        for (i = 0; i < visible.length; i++) {
            if (insertBefore) row.insertBefore(visible[i], insertBefore);
            else row.appendChild(visible[i]);
        }
        for (i = 0; i < hidden.length; i++) {
            if (insertBefore) row.insertBefore(hidden[i], insertBefore);
            else row.appendChild(hidden[i]);
        }

        // Keep map column last in map views
        if (mapCol) row.appendChild(mapCol);
    }
}

function cleanText(text) {
    return String(text || '')
        .toLowerCase()
        .replace(/['’]/g, "'")
        .replace(/\s+/g, ' ')
        .trim();
}

function textsMatch(a, b) {
    var x = cleanText(a);
    var y = cleanText(b);
    if (x === y) return true;
    if (x.indexOf(y) !== -1) return true;
    if (y.indexOf(x) !== -1) return true;
    return false;
}

// Create one pill in the left box
function addPill(input) {
    var box = getPillBox(input.name);
    if (!box) return;

    var pills = box.querySelectorAll('.selected-item');
    var i;

    // Sort is radio -> only one pill allowed
    if (input.type === 'radio') {
        for (i = 0; i < pills.length; i++) {
            if (pills[i].getAttribute('data-group') === input.name) {
                pills[i].remove();
            }
        }
    } else {
        // Venue checkbox -> do not duplicate
        for (i = 0; i < pills.length; i++) {
            if (
                pills[i].getAttribute('data-group') === input.name &&
                pills[i].getAttribute('data-value') === input.value
            ) {
                return;
            }
        }
    }

    var pill = document.createElement('div');
    pill.className = 'selected-item cursor d-flex align-items-center gap-10';
    pill.setAttribute('data-group', input.name);
    pill.setAttribute('data-value', input.value);
    pill.setAttribute('data-dynamic', 'true');
    pill.innerHTML =
        '<p>' + getOptionLabel(input) + '</p>' +
        '<img src="' + getCrossIcon() + '" class="cross svg" alt="remove">';

    box.appendChild(pill);
}

// Remove matching pill
function removePill(input) {
    var box = getPillBox(input.name);
    if (!box) return;

    var pills = box.querySelectorAll('.selected-item');
    var i;
    for (i = 0; i < pills.length; i++) {
        if (
            pills[i].getAttribute('data-group') === input.name &&
            pills[i].getAttribute('data-value') === input.value
        ) {
            pills[i].remove();
            return;
        }
    }
}

// ---------- Open / close Venue + Sort dropdowns ----------
var sections = document.querySelectorAll('.venu-sorting-section');
var s;
for (s = 0; s < sections.length; s++) {
    (function (container) {
        var sortBy = container.querySelector('.sort-by');
        var sortMenu = container.querySelector('.sort-by-filter');
        var venueBy = container.querySelector('.venue-selection');
        var venueMenu = container.querySelector('.venue-list');

        if (!sortBy || !sortMenu || !venueBy || !venueMenu) return;

        sortBy.addEventListener('click', function (e) {
            // clicking inside open menu should not toggle closed via button logic
            if (e.target.closest('.sort-by-filter')) return;

            var isOpen = sortMenu.style.display === 'block';
            closeAllVenueSortDropdowns();
            sortMenu.style.display = isOpen ? 'none' : 'block';
        });

        venueBy.addEventListener('click', function (e) {
            if (e.target.closest('.venue-list')) return;

            var isOpen = venueMenu.style.display === 'block';
            closeAllVenueSortDropdowns();
            venueMenu.style.display = isOpen ? 'none' : 'block';
        });
    })(sections[s]);
}

// Click outside -> close menus
document.addEventListener('click', function (e) {
    if (e.target.closest('.venue-selection, .sort-by')) return;
    closeAllVenueSortDropdowns();
}, true);

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAllVenueSortDropdowns();
});

// ---------- Demo values on static cards (so filter/sort is visible) ----------
function prepareDemoCards(area) {
    if (!area) return;

    // prepare ALL views (calendar + list + map list), each separately
    var panels = area.querySelectorAll('.tabcontent');
    var p;
    if (!panels.length) {
        prepareDemoCardsInRoot(area, area.id);
        return;
    }
    for (p = 0; p < panels.length; p++) {
        prepareDemoCardsInRoot(panels[p], area.id);
    }
}

function prepareDemoCardsInRoot(root, areaId) {
    var cards = getResultCards(root);
    var i;

    var groomerTags = [
        ['Home Visit', 'Mobile Station'],
        ['Salons'],
        ["Groomer's studio"],
        ['Visiting Groomers'],
        ['Mobile Station'],
        ["Groomer's studio", 'Home Visit']
    ];
    var spaceTags = [
        ['Salon'],
        ['Garden / Shed'],
        ['Private rooms'],
        ['Mobile station'],
        ['Others'],
        ['Salon', 'Private rooms']
    ];

    for (i = 0; i < cards.length; i++) {
        var card = cards[i];
        if (card.getAttribute('data-ready') === '1') continue;

        card.setAttribute('data-ready', '1');
        card.setAttribute('data-order', String(i));
        card.setAttribute('data-price', String(30 + i * 7));
        card.setAttribute('data-distance', String((1 + i * 0.6).toFixed(1)));
        card.setAttribute('data-soonest', String(i));

        var priceEl = card.querySelector('.price span');
        var distEl = card.querySelector('.distance span');
        if (priceEl) priceEl.textContent = '£' + card.getAttribute('data-price');
        if (distEl) distEl.textContent = card.getAttribute('data-distance') + ' mi';

        var tagList = (areaId === 'space') ? spaceTags : groomerTags;
        var pick = tagList[i % tagList.length];
        var wrap = card.querySelector('.tags');
        if (wrap) {
            var html = '';
            var j;
            for (j = 0; j < pick.length; j++) {
                html += '<div class="tag">' + pick[j] + '</div>';
            }
            wrap.innerHTML = html;
            card.setAttribute('data-venues', pick.join('|'));
        }
    }
}

// ---------- VENUE FILTER: show/hide cards ----------
function filterByVenue(inputName) {
    var area = getResultsArea(inputName);
    if (!area) return;

    // All checked venue checkboxes
    var checkedInputs = document.querySelectorAll('input[name="' + inputName + '"]:checked');
    var selected = [];
    var i;
    for (i = 0; i < checkedInputs.length; i++) {
        selected.push(checkedInputs[i].value);
    }

    // Filter every view panel, but count only the active one
    var panels = area.querySelectorAll('.tabcontent');
    if (!panels.length) {
        applyVenueFilterToRoot(area, selected);
        packVisibleColumns(area);
        return;
    }

    for (i = 0; i < panels.length; i++) {
        applyVenueFilterToRoot(panels[i], selected);
        packVisibleColumns(panels[i]);
    }

    var active = getActivePanel(area);
    var activeCards = getResultCards(active);
    var visibleCount = 0;
    for (i = 0; i < activeCards.length; i++) {
        var col = getCardColumn(activeCards[i]);
        if (col.style.display !== 'none') visibleCount++;
    }

    var countBox = area.querySelector('.heading-count .count');
    if (countBox) {
        countBox.textContent = String(visibleCount);
    }
}

function applyVenueFilterToRoot(root, selected) {
    var cards = getResultCards(root);
    var i;

    for (i = 0; i < cards.length; i++) {
        var card = cards[i];
        var col = getCardColumn(card);

        var tags = [];
        var saved = card.getAttribute('data-venues') || '';
        if (saved) {
            tags = saved.split('|');
        }
        var tagEls = card.querySelectorAll('.tag');
        var t;
        for (t = 0; t < tagEls.length; t++) {
            tags.push(tagEls[t].textContent.trim());
        }

        var show = false;
        if (selected.length === 0) {
            show = true;
        } else {
            var s;
            for (s = 0; s < selected.length; s++) {
                var k;
                for (k = 0; k < tags.length; k++) {
                    if (textsMatch(selected[s], tags[k])) {
                        show = true;
                        break;
                    }
                }
                if (show) break;
            }
        }

        col.style.display = show ? '' : 'none';
    }
}

// ---------- SORT: reorder cards ----------
function sortResults(radio) {
    var area = getResultsArea(radio.name);
    if (!area) return;

    var panels = area.querySelectorAll('.tabcontent');
    var i;
    if (!panels.length) {
        sortResultsInRoot(area, radio.value);
        return;
    }
    for (i = 0; i < panels.length; i++) {
        sortResultsInRoot(panels[i], radio.value);
    }
}

function sortResultsInRoot(root, mode) {
    var cards = getResultCards(root);

    cards.sort(function (a, b) {
        if (mode === 'distance') {
            return Number(a.getAttribute('data-distance')) - Number(b.getAttribute('data-distance'));
        }
        if (mode === 'lowest_price') {
            return Number(a.getAttribute('data-price')) - Number(b.getAttribute('data-price'));
        }
        if (mode === 'soonest_available') {
            return Number(a.getAttribute('data-soonest')) - Number(b.getAttribute('data-soonest'));
        }
        return Number(a.getAttribute('data-order')) - Number(b.getAttribute('data-order'));
    });

    var i;
    for (i = 0; i < cards.length; i++) {
        var col = getCardColumn(cards[i]);
        var parent = col.parentNode;
        if (!parent) continue;

        var loadMore = parent.querySelector(':scope > .col-lg-12');
        if (loadMore) {
            parent.insertBefore(col, loadMore);
        } else {
            parent.appendChild(col);
        }
    }

    packVisibleColumns(root);
}

function resetSort(groupName) {
    var def = document.querySelector('input[name="' + groupName + '"][value="default"]');
    if (!def) return;
    def.checked = true;
    sortResults(def);
    addPill(def);
}

// ---------- Start everything when page is ready ----------
document.addEventListener('DOMContentLoaded', function () {
    var groomerArea = document.getElementById('groomer');
    var spaceArea = document.getElementById('space');

    prepareDemoCards(groomerArea);
    prepareDemoCards(spaceArea);

    // Venue checkboxes
    var venueInputs = document.querySelectorAll(
        'input[name="groomer-venue[]"], input[name="space-venue[]"]'
    );
    var i;
    for (i = 0; i < venueInputs.length; i++) {
        (function (input) {
            if (input.checked) {
                addPill(input);
            }
            input.addEventListener('change', function () {
                if (input.checked) {
                    addPill(input);
                } else {
                    removePill(input);
                }
                filterByVenue(input.name);
            });
        })(venueInputs[i]);
    }

    // Sort radios
    var sortInputs = document.querySelectorAll(
        'input[name="groomer-sort"], input[name="space-sort"]'
    );
    for (i = 0; i < sortInputs.length; i++) {
        (function (radio) {
            if (radio.checked) {
                addPill(radio);
            }
            radio.addEventListener('change', function () {
                if (!radio.checked) return;

                var menu = radio.closest('.sort-by-filter');
                if (menu) menu.style.display = 'none';

                addPill(radio);
                sortResults(radio);
            });
        })(sortInputs[i]);
    }

    filterByVenue('groomer-venue[]');
    filterByVenue('space-venue[]');
});

// Click X on a pill
document.addEventListener('click', function (e) {
    var pill = e.target.closest('.selected-item');
    if (!pill) return;

    var group = pill.getAttribute('data-group');
    var value = pill.getAttribute('data-value');

    // Venue pill
    if (group === 'groomer-venue[]' || group === 'space-venue[]') {
        e.preventDefault();
        e.stopPropagation();

        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        var i;
        for (i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].name === group && checkboxes[i].value === value) {
                checkboxes[i].checked = false;
                break;
            }
        }

        pill.remove();
        filterByVenue(group);
        return;
    }

    // Sort pill -> back to Recommended
    if (group === 'groomer-sort' || group === 'space-sort') {
        e.preventDefault();
        e.stopPropagation();
        resetSort(group);
    }
});

// venue filter ends



// main tabs content view starts

const main_tabs = document.querySelectorAll('.top-tabs');
const groomer_form_fields = document.querySelector('.find-groomer-search-content-area');
const space_form_fields = document.querySelector('.find-space-search-content-area');

main_tabs.forEach(button => {
    button.addEventListener('click', () => {
        const tabName = button.dataset.section;
        const isGroomer = tabName === 'groomer';

        if (groomer_form_fields && space_form_fields) {
            groomer_form_fields.style.display = isGroomer ? 'block' : 'none';
            space_form_fields.style.display = isGroomer ? 'none' : 'block';
        }

        // Hide all tab contents
        document.querySelectorAll('.main-tab-content').forEach(tc => {
            tc.style.display = 'none';
            tc.classList.remove('active');
        });

        // Toggle active on tab text (same as home page), not the wrapper div
        main_tabs.forEach(btn => {
            btn.querySelector('.find-groomer-space-text')?.classList.remove('active');
        });
        button.querySelector('.find-groomer-space-text')?.classList.add('active');

        // Show clicked tab content
        const tabContent = document.getElementById(tabName);
        if (tabContent) {
            tabContent.style.display = 'block';
        }
    });
});

// Honor server-selected Find Groomer / Find Space tab on first paint
(function activatePresetMainTab() {
    const preset = document.querySelector('.top-tabs .find-groomer-space-text.active');
    const button = preset?.closest('.top-tabs');
    if (!button || !button.dataset.section) return;

    const tabName = button.dataset.section;
    const isGroomer = tabName === 'groomer';

    if (groomer_form_fields && space_form_fields) {
        groomer_form_fields.style.display = isGroomer ? 'block' : 'none';
        space_form_fields.style.display = isGroomer ? 'none' : 'block';
    }

    document.querySelectorAll('.main-tab-content').forEach(tc => {
        tc.style.display = 'none';
        tc.classList.remove('active');
    });

    const tabContent = document.getElementById(tabName);
    if (tabContent) {
        tabContent.style.display = 'block';
        tabContent.classList.add('active');
    }
})();

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

// inner tabs content view ends (handled once in map/tabs init above)



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

// Mini-search: close every dropdown, then open only what was clicked
(function () {
    const PET_MENUS = [
        ['petTypeToggle', 'petTypeOptions'],
        ['petSizeToggle', 'petSizeOptions'],
        ['spaceTypeToggle', 'spaceTypeOptions'],
        ['spaceSizeToggle', 'spaceSizeOptions'],
    ];

    function closeAll() {
        document.querySelectorAll('.mini-search-widget .custom-select.open')
            .forEach((el) => el.classList.remove('open'));

        document.querySelectorAll('.mini-search-widget .popover')
            .forEach((el) => { el.style.display = 'none'; });

        document.querySelectorAll('.mini-search-widget .field.date, .mini-search-widget .field.time')
            .forEach((el) => {
                el.classList.remove('focused');
                el.querySelector('.input-row')?.setAttribute('aria-expanded', 'false');
            });

        PET_MENUS.forEach(([toggleId, menuId]) => {
            const menu = document.getElementById(menuId);
            const toggle = document.getElementById(toggleId);
            if (menu) menu.style.display = 'none';
            if (toggle) toggle.style.borderRadius = '10px';
        });
    }

    // Pet type / size menus
    PET_MENUS.forEach(([toggleId, menuId]) => {
        const toggle = document.getElementById(toggleId);
        const menu = document.getElementById(menuId);
        if (!toggle || !menu) return;

        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const wasOpen = menu.style.display === 'block';
            closeAll();
            if (!wasOpen) {
                menu.style.display = 'block';
                toggle.style.borderRadius = '10px 10px 0 0';
            }
        });
    });

    // Service type dropdown
    document.querySelectorAll('.service-type-select .custom-select').forEach((select) => {
        const trigger = select.querySelector('.select-trigger');
        const items = select.querySelectorAll('.select-options li');
        const label = select.querySelector('.selected-text');
        const input = select.querySelector('input[type="hidden"]');
        if (!trigger) return;

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const wasOpen = select.classList.contains('open');
            closeAll();
            if (!wasOpen) select.classList.add('open');
        });

        items.forEach((item) => {
            item.addEventListener('click', () => {
                label.textContent = item.textContent;
                input.value = item.dataset.value;
                select.classList.remove('open');
                select.classList.add('has-value');
            });
        });
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.mini-search-widget')) closeAll();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAll();
    });

    window.closeMiniSearchDropdowns = closeAll;
})();

// date time picker js  

(function () {
    const datetimeWrappers = document.querySelectorAll('.datetime-wrapper');

    datetimeWrappers.forEach((wrapper) => {
        const dateField = wrapper.querySelector('.field.date');
        const timeField = wrapper.querySelector('.field.time');
        if (!dateField || !timeField) return;

        const dateInput = dateField.querySelector('.fake-input');
        const datePopover = dateField.querySelector('.date-popover');
        const timeInput = timeField.querySelector('.fake-input');
        const timePopover = timeField.querySelector('.time-popover');
        if (!datePopover || !timePopover || !dateInput || !timeInput) return;

        const daysGrid = datePopover.querySelector('.days-grid');
        const monthLabel = datePopover.querySelector('.month-label');
        const prevMonthBtn = datePopover.querySelector('.prev-month');
        const nextMonthBtn = datePopover.querySelector('.next-month');
        const weekdayRow = datePopover.querySelector('.weekday-row');
        const timeList = timePopover.querySelector('.time-list');
        if (!daysGrid || !monthLabel || !prevMonthBtn || !nextMonthBtn || !weekdayRow || !timeList) return;

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
        function closeAllPopovers() {
            window.closeMiniSearchDropdowns?.();
        }

        function toggleDatePopover() {
            const wasOpen = datePopover.style.display === 'block';
            closeAllPopovers();
            if (!wasOpen) {
                datePopover.style.display = 'block';
                dateField.classList.add('focused');
                dateField.querySelector('.input-row').setAttribute('aria-expanded', 'true');
            }
        }

        function toggleTimePopover() {
            const wasOpen = timePopover.style.display === 'block';
            closeAllPopovers();
            if (!wasOpen) {
                timePopover.style.display = 'block';
                timeField.classList.add('focused');
                timeField.querySelector('.input-row').setAttribute('aria-expanded', 'true');

                const el = timeList.querySelector(`.time-item[data-time="${selectedTime}"]`);
                if (el) el.scrollIntoView({ block: 'center' });
            }
        }

        /* ---------------------- Events ---------------------- */
        dateField.querySelector('.input-row').addEventListener('click', (e) => {
            e.stopPropagation();
            toggleDatePopover();
        });

        timeField.querySelector('.input-row').addEventListener('click', (e) => {
            e.stopPropagation();
            toggleTimePopover();
        });

        datePopover.addEventListener('click', (e) => e.stopPropagation());
        timePopover.addEventListener('click', (e) => e.stopPropagation());

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
            }
        }

        init();
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

        const weekEl = calendar.querySelector('.week');
        const wrapEl = calendar.querySelector('.week-container');
        const rangeEl = calendar.querySelector('.range');
        const monthEl = calendar.querySelector('.month');
        const prevBtn = calendar.querySelector('.prev');
        const nextBtn = calendar.querySelector('.next');

        const addDays = (d, n) => new Date(d.getFullYear(), d.getMonth(), d.getDate() + n);
        const pad = n => String(n).padStart(2, '0');
        const isSameDay = (a, b) =>
            a.getFullYear() === b.getFullYear() &&
            a.getMonth() === b.getMonth() &&
            a.getDate() === b.getDate();

        const today = new Date();
        let selectedDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        let dragging = false;
        let startX = 0;
        let moved = false;
        const CLICK_THRESHOLD = 6;

        // 13 columns: 3 hidden left | 7 visible | 3 hidden right
        const VISIBLE = 7;
        const EXTRA = 3;
        const TOTAL = VISIBLE + EXTRA * 2;          // 13
        const CENTER = EXTRA + Math.floor(VISIBLE / 2); // index 6 = active day

        const EASE = 'cubic-bezier(0.22, 0.9, 0.25, 1)';
        const DUR = '0.25s';

        let sw = 0; // slot width in px, computed per render

        // â”€â”€ Transform helpers â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        const restTx = () => -EXTRA * sw; // offset that shows center 7

        function setTx(x, animate = false) {
            weekEl.style.transition = animate ? `transform ${DUR} ${EASE}` : 'none';
            weekEl.style.transform = `translateX(${x}px)`;
        }

        function afterTransition(fn) {
            let done = false;
            const run = () => { if (done) return; done = true; fn(); };
            weekEl.addEventListener('transitionend', run, { once: true });
            setTimeout(run, 350);
        }

        // â”€â”€ Header â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function updateHeader() {
            const start = addDays(selectedDate, -3);
            const end = addDays(selectedDate, 3);
            const sm = start.toLocaleString(undefined, { month: 'long' });
            const em = end.toLocaleString(undefined, { month: 'long' });
            rangeEl.textContent =
                `${pad(start.getDate())} – ${pad(end.getDate())} ` +
                (sm === em ? sm : `${sm} / ${em}`);
            monthEl.textContent =
                selectedDate.toLocaleString(undefined, { month: 'long', year: 'numeric' });
        }

        // â”€â”€ Build 13-column strip â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function buildWeek() {
            weekEl.innerHTML = '';

            updateHeader();

            for (let i = 0; i < TOTAL; i++) {
                const d = addDays(selectedDate, i - CENTER);
                const isActive = isSameDay(d, selectedDate);
                const col = document.createElement('div');

                col.className = 'week-days';
                col.style.width = sw + 'px';

                col.innerHTML = `
                    <div class="dow ${isActive ? 'active' : ''}">
                        ${d.toLocaleString(undefined, { weekday: 'short' })}
                    </div>
                    <div class="date ${isActive ? 'active' : ''}">
                        ${pad(d.getDate())}
                    </div>`;

                const dayDiff = i - CENTER;

                col.addEventListener('click', () => {
                    if (moved || dayDiff === 0) return;
                    selectedDate = d;
                    buildWeek();
                    // Jump to where item was, then slide to center
                    setTx(restTx() + dayDiff * sw);
                    requestAnimationFrame(() => requestAnimationFrame(() => {
                        setTx(restTx(), true);
                    }));
                });

                weekEl.appendChild(col);
            }

            setTx(restTx()); // show center 7 immediately, no animation
        }

        // â”€â”€ Slide animation helper (nav buttons) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function slideNav(direction) {
            // direction: +1 = enter from right (prev), -1 = enter from left (next)
            setTx(restTx() + direction * sw);
            requestAnimationFrame(() => requestAnimationFrame(() => {
                setTx(restTx(), true);
            }));
        }

        // â”€â”€ Nav buttons â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                selectedDate = addDays(selectedDate, -1);
                buildWeek();
                slideNav(+1);
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                selectedDate = addDays(selectedDate, +1);
                buildWeek();
                slideNav(-1);
            });
        }

        // â”€â”€ Drag â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        function onStart(x) {
            dragging = true;
            startX = x;
            moved = false;
            wrapEl.classList.add('dragging');
            setTx(restTx()); // cancel any in-flight animation
        }

        function onMove(x) {
            if (!dragging) return;
            const diff = x - startX;
            if (Math.abs(diff) > CLICK_THRESHOLD) moved = true;

            // Rubber-band resistance once past the hidden columns
            const maxDrag = EXTRA * sw;
            const drag = Math.abs(diff) > maxDrag
                ? Math.sign(diff) * (maxDrag + (Math.abs(diff) - maxDrag) * 0.15)
                : diff;

            setTx(restTx() + drag);
        }

        function onEnd(x) {
            if (!dragging) return;
            dragging = false;
            wrapEl.classList.remove('dragging');

            const diff = x - startX;
            let days = Math.round(-diff / sw);
            days = Math.max(-EXTRA, Math.min(EXTRA, days));

            // Snap to nearest slot, then swap content invisibly
            setTx(restTx() - days * sw, true);
            afterTransition(() => {
                if (days !== 0) selectedDate = addDays(selectedDate, days);
                buildWeek(); // re-renders at restTx â€” invisible cut
            });
        }

        // â”€â”€ Event listeners â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
        wrapEl.addEventListener('mousedown', e => { e.preventDefault(); onStart(e.clientX); });
        window.addEventListener('mousemove', e => onMove(e.clientX));
        window.addEventListener('mouseup', e => onEnd(e.clientX));

        wrapEl.addEventListener('touchstart', e => onStart(e.touches[0].clientX), { passive: true });
        wrapEl.addEventListener('touchmove', e => onMove(e.touches[0].clientX), { passive: true });
        wrapEl.addEventListener('touchend', e => onEnd(e.changedTouches[0].clientX));

        const ro = new ResizeObserver(entries => {
            const newSw = entries[0].contentRect.width / VISIBLE;
            if (newSw === sw || newSw === 0) return; // nothing changed or still hidden
            sw = newSw;
            buildWeek();
        });

        ro.observe(wrapEl);
    });
});


// modal js starts 

// Open modal
document.addEventListener('click', e => {
    const openBtn = e.target.closest('[data-modal-open]');
    if (!openBtn) return;

    const modalId = openBtn.dataset.modalOpen;
    const modal = document.getElementById(modalId);
    if (!modal) return;

    modal.style.display = 'flex';

    // Recalculate bubble position once the modal has real width
    modal.querySelectorAll('.range-slider input[type="range"]').forEach(range => {
        range.dispatchEvent(new Event('input', { bubbles: true }));
    });
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

// Clear All filters inside groom / space modals
document.addEventListener('click', e => {
    const clearBtn = e.target.closest('#groomModal .modal-footer-btn.clear, #spaceModal .modal-footer-btn.clear');
    if (!clearBtn) return;

    const modal = clearBtn.closest('.modal');
    if (!modal) return;

    modal.querySelectorAll('.filter-options-section input[type="checkbox"]').forEach(input => {
        input.checked = false;
    });

    const range = modal.querySelector('.range-slider input[type="range"]');
    if (range) {
        range.value = range.max;
        range.dispatchEvent(new Event('input', { bubbles: true }));
    }
});

// modal js ends 


// range js starts 

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.range-slider').forEach(slider => {
        const range = slider.querySelector('input[type="range"]');
        const output = slider.querySelector('.output');
        const inclRange = slider.querySelector('.incl-range');
        const maxPrice = slider.querySelector('.max-price');
        const min = Number(range.min) || 0;
        const max = Number(range.max);

        function updateView() {
            const value = Number(range.value);
            const ratio = max === min ? 0 : (value - min) / (max - min);

            // Same thumb math in Chrome + Firefox (thumb is inset by half its size)
            const thumbWidth = parseFloat(getComputedStyle(range).getPropertyValue('--range-thumb-size')) || 24;
            const trackWidth = range.offsetWidth || slider.offsetWidth;
            const thumbCenterPx = ratio * (trackWidth - thumbWidth) + thumbWidth / 2;

            output.textContent = '£' + value;
            // px + translateX(-50%) keeps bubble centered on thumb in both browsers
            output.style.left = thumbCenterPx + 'px';
            inclRange.style.width = thumbCenterPx + 'px';

            if (maxPrice) {
                maxPrice.style.visibility = value >= max ? 'hidden' : 'visible';
            }
        }

        updateView();
        range.addEventListener('input', updateView);
        range.addEventListener('change', updateView); // Firefox also fires change
        window.addEventListener('resize', updateView);
    });

});

// range js ends  


// search bar widget sticky + filter bar offset below it
(function () {
    const stickyBar = document.querySelector('.sticky-search');
    if (!stickyBar) return;

    const siteHeader = document.querySelector('header');
    let cachedHeaderHeight = 0;
    let isCompact = false;
    let isAdjustingScroll = false;

    function updateCachedHeaderHeight() {
        cachedHeaderHeight = siteHeader ? siteHeader.offsetHeight : 0;
    }

    function setStickyOffsets() {
        document.documentElement.style.setProperty(
            '--header-height',
            cachedHeaderHeight + 'px'
        );
        document.documentElement.style.setProperty(
            '--sticky-search-offset',
            (cachedHeaderHeight + stickyBar.offsetHeight) + 'px'
        );
    }

    function setCompact(nextCompact) {
        if (nextCompact === isCompact) return;

        const scrollBefore = window.scrollY;
        const heightBefore = stickyBar.offsetHeight;

        isCompact = nextCompact;
        stickyBar.classList.toggle('scrolled', isCompact);

        // Hiding tabs shrinks the bar and shifts scrollY — compensate so
        // scrollbar dragging does not bounce around the compact threshold.
        void stickyBar.offsetHeight;
        const heightDelta = heightBefore - stickyBar.offsetHeight;

        if (heightDelta !== 0) {
            isAdjustingScroll = true;
            window.scrollTo(0, scrollBefore - heightDelta);
            requestAnimationFrame(() => {
                isAdjustingScroll = false;
            });
        }

        setStickyOffsets();
    }

    function onScroll() {
        if (isAdjustingScroll) return;

        const enterAt = Math.max(cachedHeaderHeight + 30, 50);
        const exitAt = Math.max(cachedHeaderHeight - 80, 0);

        if (!isCompact && window.scrollY > enterAt) {
            setCompact(true);
        } else if (isCompact && window.scrollY < exitAt) {
            setCompact(false);
        }
    }

    function onResize() {
        updateCachedHeaderHeight();
        setStickyOffsets();
        onScroll();
    }

    updateCachedHeaderHeight();
    setStickyOffsets();
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize);

    // Sync offsets when search bar height changes (pet dropdowns)
    if (typeof ResizeObserver !== 'undefined') {
        const ro = new ResizeObserver(setStickyOffsets);
        ro.observe(stickyBar);
    }
})();  
