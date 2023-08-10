let selectElement = document.getElementById("city");

let city = "";
let country = "";
let language = 'french';

selectElement.addEventListener("change", (event)=>{
    switch (event.target.value) {
        case 'grenoble':
            city = "Grenoble";
            country = "France";
            language = 'french';
            break;
        case 'crest':
            city = "Crest";
            country = "France";
            language = 'french';
            break;
        case 'opole':
            city = "Opole";
            country = "Poland";
            language = 'english';
            break;
        default:

    }

    let api_url = "https://www.weatherwp.com/api/common/publicWeatherForLocation.php?city="+ city + "&country=" + country + "&language=" + language;
    getapi(api_url);
})

async function getapi(url) {

    // Storing response
    const response = await fetch(url);

    // Storing data in form of JSON
    var data = await response.json();
    console.log(data);
    show(data);
}

async function show(data) {
    console.log(data);
    let getThis = document.getElementById("cnalps-weather-widget");
    let temperature = data["temp"];
    let description = data["description"];
    let icon_url = data["icon"];
    let content = "<div>Météo à " + city + "</div>" +
        "<div> " + temperature + "°C - " + description + "</div>" +
        "<img src='" + icon_url +"' alt='Weather Icon'>";

    getThis.innerHTML = content;
}