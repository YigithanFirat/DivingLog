let map;
let markers = [];
let selectedMarker = null;

const coves = [
    { name: "İzmir Körfezi", lat: 38.4192, lng: 27.1287 },
    { name: "Gökova Körfezi", lat: 36.9500, lng: 28.0000 },
    { name: "Edremit Körfezi", lat: 39.5500, lng: 26.8000 },
    { name: "Antalya Körfezi", lat: 36.8500, lng: 30.7000 },
    { name: "Saros Körfezi", lat: 40.4000, lng: 26.8000 },
    { name: "Mersin Körfezi", lat: 36.8000, lng: 34.6000 },
    { name: "İskenderun Körfezi", lat: 36.6000, lng: 36.2000 }
];

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 38.5, lng: 32.0 },
        zoom: 6
    });

    coves.forEach(cove => {
        const marker = new google.maps.Marker({
            position: { lat: cove.lat, lng: cove.lng },
            map: map,
            title: cove.name
        });

        marker.addListener("click", () => {
            if (selectedMarker) {
                selectedMarker.setAnimation(null);
            }
            marker.setAnimation(google.maps.Animation.BOUNCE);
            selectedMarker = marker;
            document.getElementById("diving_location").value = cove.name;
        });

        markers.push(marker);
    });

    map.addListener("click", function(event) {
        const lat = event.latLng.lat();
        const lng = event.latLng.lng();

        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: { lat, lng } }, (results, status) => {
            if (status === "OK" && results[0]) {
                document.getElementById("diving_location").value = results[0].formatted_address;
            } else {
                document.getElementById("diving_location").value = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            }
        });
    });
}