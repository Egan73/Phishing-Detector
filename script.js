const materialButtons = document.querySelectorAll('.material-button');
const materialDetails = document.querySelector('.material-details');

materialButtons.forEach(button => {
  button.addEventListener('click', () => {
    const materialName = button.dataset.material;
    
    // Replace this with actual description data
    let materialDescription = "";
    if (materialName === "Material 1") {
      materialDescription = "Phishing is a type of cyber attack in which attackers attempt to deceive individuals into providing sensitive information, such as usernames, passwords, credit card numbers, or other personal details. These attacks typically involve fraudulent emails, messages, or websites that appear to come from legitimate sources.";
    } else if (materialName === "Material 2") {
      materialDescription = "1. Spear Phishing: Targeted phishing attacks aimed at specific individuals or organizations. These attacks are more personalized and often harder to detect. <br> <br> 2. Whaling: A form of spear phishing that targets high-profile individuals such as executives or public figures.";
    } else if (materialName === "Material 3") {
      materialDescription = "1. Deceptive Emails: Phishing emails often look like they come from trusted entities like banks, social media platforms, online stores, or government agencies. They may use logos, colors, and language similar to those of the legitimate organization.<br> <br> 2. Malicious Links:These emails usually contain links that lead to fake websites designed to capture the victim's information. These websites mimic the appearance of the real websites they are impersonating";
    } else if (materialName === "Material 4") {
      materialDescription = "1. Education and Awareness: Educating users about the signs of phishing attacks and promoting a cautious approach to unsolicited communications.<br><br> 2. Email Filtering: Implementing robust spam filters that can identify and block phishing emails before they reach users' inboxes.";
    } else if (materialName === "Material 5") {
      materialDescription = "1. Use Strong, Unique Passwords: Employ a password manager to maintain strong, unique passwords for each account. <br> <br> 2. Enable Two-Factor Authentication (2FA): Add an extra layer of security to your accounts. <br><br> 3. Keep Software Updated: Regularly update your operating system, browsers, and security software.";
    }
    materialDetails.innerHTML = materialDescription;
  });
});