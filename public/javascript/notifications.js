function updateNotifications() {
    // Fetch the notifications
    fetch('/notifications')
        .then(response => response.json())
        .then(notifications => {
            // Clear the dropdown menu
            document.querySelector('.dropdown-menu').innerHTML = '';

            // Add the notifications to the dropdown menu
            notifications.forEach(notification => {
                const notificationElement = document.createElement('a');
                notificationElement.classList.add('dropdown-item');
                notificationElement.innerHTML = notification.data.message;
                document.querySelector('.dropdown-menu').appendChild(notificationElement);
            });
        });
}

updateNotifications();
setInterval(updateNotifications, 10000); // Update the notifications every 10 seconds
