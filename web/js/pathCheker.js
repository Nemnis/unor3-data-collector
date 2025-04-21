// get current url location
const currentUrl = window.location.href;

// links in nav
const collectorLink = document.getElementsByClassName("link-data-collector")[0];
const homeLink = document.getElementsByClassName("link-home")[0];

console.log(currentUrl);

switch (currentUrl) {
    case 'http://localhost/':
        homeLink.classList.add('active');
        break;
    
    case 'http://localhost/collector.php':
        collectorLink.classList.add('active');
        break;
}

// Production
/*switch (currentUrl) {
    case 'https://luisfelipelugo.com/':
        homeLink.classList.add('active');
        break;
    
    case 'https://luisfelipelugo.com/collector.php':
        collectorLink.classList.add('active');
        break;
}*/