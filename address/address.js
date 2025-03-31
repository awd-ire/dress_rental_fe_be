        function deleteAddress(addressId) {
            if (confirm("Are you sure you want to delete this address?")) {
                fetch('delete_address.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'address_id=' + addressId
                })
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === "success") {
                        document.getElementById("address-" + addressId).remove();
                    } else {
                        alert("Error deleting address.");
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }
