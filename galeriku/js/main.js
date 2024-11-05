// Modal functionality
const modal = document.getElementById('imageModal');
const modalImg = document.getElementById('modalImage');
const modalTitle = document.getElementById('modalTitle');
const modalDescription = document.getElementById('modalDescription');
const modalPostedBy = document.getElementById('modalPostedBy');
const modalActions = document.getElementById('modalActions');
const closeBtn = document.getElementsByClassName('close')[0];
const likeButton = document.getElementById('likeButton');
const likeCount = document.getElementById('likeCount');
const commentForm = document.getElementById('commentForm');
const commentsContainer = document.getElementById('commentsContainer');

function openModal(imageSrc, title, description, username, imageId, userId, currentUserId) {
    modal.style.display = "block";
    modalImg.src = imageSrc;
    modalTitle.textContent = title;
    modalDescription.textContent = description;
    modalPostedBy.textContent = `Posted by: ${username}`;
    document.getElementById('imageId').value = imageId;
    
    // Load likes and comments
    loadLikes(imageId);
    loadComments(imageId);
    
    if(currentUserId && userId === currentUserId) {
        modalActions.innerHTML = `
            <a href="edit.php?id=${imageId}" class="edit-btn">Edit</a>
            <a href="delete.php?id=${imageId}" class="delete-btn" 
               onclick="return confirm('Are you sure you want to delete this image?')">Delete</a>
        `;
    } else {
        modalActions.innerHTML = '';
    }
    
    document.body.style.overflow = 'hidden';
}

// Close modal functions
closeBtn.onclick = function() {
    modal.style.display = "none";
    document.body.style.overflow = 'auto';
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
        document.body.style.overflow = 'auto';
    }
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape" && modal.style.display === "block") {
        modal.style.display = "none";
        document.body.style.overflow = 'auto';
    }
});

// Like functionality
async function loadLikes(imageId) {
    try {
        const response = await fetch(`api/likes.php?image_id=${imageId}`);
        const data = await response.json();
        likeCount.textContent = data.likes;
        likeButton.classList.toggle('liked', data.userLiked);
    } catch (error) {
        console.error('Error loading likes:', error);
    }
}

likeButton.onclick = async function() {
    const imageId = document.getElementById('imageId').value;
    try {
        const response = await fetch('api/likes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ image_id: imageId })
        });
        const data = await response.json();
        likeCount.textContent = data.likes;
        likeButton.classList.toggle('liked', data.userLiked);
    } catch (error) {
        console.error('Error toggling like:', error);
    }
}

// Comments functionality
async function loadComments(imageId) {
    try {
        const response = await fetch(`api/comments.php?image_id=${imageId}`);
        const comments = await response.json();
        commentsContainer.innerHTML = comments.map(comment => `
            <div class="comment">
                <div class="comment-header">
                    <span class="comment-user">${comment.username}</span>
                    <span class="comment-date">${comment.created_at}</span>
                </div>
                <p class="comment-text">${comment.comment}</p>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading comments:', error);
    }
}

if (commentForm) {
    commentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const imageId = document.getElementById('imageId').value;
        const comment = this.querySelector('textarea').value;
        
        try {
            const response = await fetch('api/comments.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    image_id: imageId,
                    comment: comment
                })
            });
            
            if (response.ok) {
                this.querySelector('textarea').value = '';
                loadComments(imageId);
            }
        } catch (error) {
            console.error('Error posting comment:', error);
        }
    });
}