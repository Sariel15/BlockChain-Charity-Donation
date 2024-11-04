// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract CharityDonationTracker {
    struct Donation {
        address donor;
        uint256 amount;
    }
    
    uint public totalDonations;

    event DonationReceived(address indexed donor, uint amount);

    Donation[] public donations;

    function donate() public payable {
        require(msg.value > 0, "Donation must be greater than 0");
        donations.push(Donation(msg.sender, msg.value));
        totalDonations += msg.value;

        emit DonationReceived(msg.sender, msg.value);
    }

    function getDonation(uint256 index) public view returns (address, uint256) {
        require(index < donations.length, "Invalid index");
        return (donations[index].donor, donations[index].amount);
    }

    function getDonationCount() public view returns (uint256) {
        return donations.length;
    }

    function getBalance() public view returns (uint256) {
        return address(this).balance;
    }
}
