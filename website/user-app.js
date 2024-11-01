let web3;
let contract;
const contractAddress = '0xe46197de82D9e851b9C59d3476bb5E6db2504bF0';  // Replace with your deployed contract address
const contractABI = [
	{
		"inputs": [],
		"name": "donate",
		"outputs": [],
		"stateMutability": "payable",
		"type": "function"
	},
	{
		"anonymous": false,
		"inputs": [
			{
				"indexed": true,
				"internalType": "address",
				"name": "donor",
				"type": "address"
			},
			{
				"indexed": false,
				"internalType": "uint256",
				"name": "amount",
				"type": "uint256"
			}
		],
		"name": "DonationReceived",
		"type": "event"
	},
	{
		"inputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"name": "donations",
		"outputs": [
			{
				"internalType": "address",
				"name": "donor",
				"type": "address"
			},
			{
				"internalType": "uint256",
				"name": "amount",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [],
		"name": "getBalance",
		"outputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [
			{
				"internalType": "uint256",
				"name": "index",
				"type": "uint256"
			}
		],
		"name": "getDonation",
		"outputs": [
			{
				"internalType": "address",
				"name": "",
				"type": "address"
			},
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [],
		"name": "getDonationCount",
		"outputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	},
	{
		"inputs": [],
		"name": "totalDonations",
		"outputs": [
			{
				"internalType": "uint256",
				"name": "",
				"type": "uint256"
			}
		],
		"stateMutability": "view",
		"type": "function"
	}
];

window.onload = async function () {
    if (typeof window.ethereum !== 'undefined') {
        try {
            web3 = new Web3(window.ethereum);
            await ethereum.request({ method: 'eth_requestAccounts' });

            contract = new web3.eth.Contract(contractABI, contractAddress);
            document.getElementById('status').innerText = "Connected to MetaMask!";
        } catch (error) {
            console.error("User denied account access:", error);
            document.getElementById('status').innerText = "Please connect MetaMask!";
        }
    } else {
        document.getElementById('status').innerText = "MetaMask not detected!";
    }
};

document.getElementById('donate-button').addEventListener('click', async () => {
    const metamaskAddress = document.getElementById('metamask-address').innerText;
    const donationAmount = document.getElementById('donation-amount').value;
    const accounts = await web3.eth.getAccounts();

    if (!donationAmount || donationAmount <= 0) {
        alert("Please enter a valid donation amount.");
        return;
    }

    try {
        const tx = await contract.methods.donate().send({
            from: accounts[0], 
            to: metamaskAddress, 
            value: web3.utils.toWei(donationAmount, 'ether'),
            gas: 500000
        });

        const transactionHash = tx.transactionHash;
        const donorAddress = accounts[0];
        const amountInEth = donationAmount;

        saveDonationToDatabase(donorAddress, amountInEth, transactionHash);

        document.getElementById('status').innerText = "Donation successful!";
        
        // Refresh the page after a successful donation
        location.reload();

    } catch (error) {
        console.error("Error making donation:", error);
        document.getElementById('status').innerText = "Failed to complete the donation.";
    }
});

function saveDonationToDatabase(donorAddress, donationAmount, transactionHash) {
    console.log("Preparing to send data to the server:", { donorAddress, donationAmount, transactionHash });

    fetch('save_donation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            donor_address: donorAddress,
            donation_amount: donationAmount,
            transaction_hash: transactionHash
        })
    })
    .then(response => response.text())  // Parse response as text
    .then(data => {
        console.log("Server response:", data);  // Log the server's response
        // Check for success response from PHP
        if (data.includes("Donation saved successfully")) {
            console.log("Donation was successfully saved in the database.");
        } else {
            console.log("Error received from the server:", data);
        }
    })
    .catch(error => {
        console.error("Error saving donation:", error);  // Log any errors
    });
}

function displayError(message) {
    document.getElementById('error-message').innerText = message;
    document.getElementById('error-message').style.display = 'block';
}
