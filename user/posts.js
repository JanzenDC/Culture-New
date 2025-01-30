$(document).ready(function() {
    let isAdmin = false;
    let currentUserId = null;
    let allPosts = [];
    let activeFilters = {
        cultureElements: [],
        learningStyles: []
    };

    function initializeFilters() {
        $('.menu-section a[href^="geography"], .menu-section a[href^="history"], .menu-section a[href^="demographics"], .menu-section a[href^="culture"]').click(function(e) {
            e.preventDefault();
            const element = $(this).text();
            
            if (activeFilters.cultureElements.includes(element)) {
                activeFilters.cultureElements = activeFilters.cultureElements.filter(item => item !== element);
                $(this).removeClass('active');
            } else {
                activeFilters.cultureElements.push(element);
                $(this).addClass('active');
            }
            
            filterAndDisplayPosts();
        });

        $('.menu-section input[type="checkbox"]').change(function() {
            const style = $(this).parent().text().trim();
            
            if (this.checked) {
                activeFilters.learningStyles.push(style);
            } else {
                activeFilters.learningStyles = activeFilters.learningStyles.filter(item => item !== style);
            }
            
            filterAndDisplayPosts();
        });
    }

    function filterAndDisplayPosts() {
        let filteredPosts = allPosts;

        if (activeFilters.cultureElements.length > 0) {
            filteredPosts = filteredPosts.filter(post => {
                const postElements = post.culture_elements ? post.culture_elements.split(',').map(e => e.trim()) : [];
                return activeFilters.cultureElements.some(filter => postElements.includes(filter));
            });
        }

        if (activeFilters.learningStyles.length > 0) {
            filteredPosts = filteredPosts.filter(post => {
                const postStyles = post.learning_styles ? post.learning_styles.split(',').map(s => s.trim()) : [];
                return activeFilters.learningStyles.some(filter => postStyles.includes(filter));
            });
        }

        displayPosts(filteredPosts);
    }

    function fetchPosts() {
        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { action: 'fetch_posts' },
            dataType: 'json',
            success: function(response) {
                currentUserId = response.current_user_id;
                isAdmin = response.isAdmin;

                allPosts = response.posts;
                filterAndDisplayPosts();
            },
            error: function(xhr, status, error) {
                console.error('Error fetching posts:', error);
            }
        });
    }

    function displayPosts(posts) {
        const postDisplay = $('#post-display');
        postDisplay.empty();

        posts.forEach(function(post) {
            const postElement = createPostElement(post);
            postDisplay.append(postElement);
        });
    }

    function createPostElement(post) {
        const comments = post.comments.map(comment => `
            <div class="comment">
                <img src="${comment.profile_picture}" alt="${comment.username}" class="comment-profile-pic">
                <div class="comment-content">
                    <strong>${comment.username}</strong>
                    <p>${comment.comment_text}</p>
                    ${comment.user_id == currentUserId ? `
                        <button class="delete-comment" data-comment-id="${comment.id}">Delete</button>
                    ` : ''}
                </div>
            </div>
        `).join('');

        const cultureElements = post.culture_elements ? `
            <div class="culture-elements">
                <strong>Culture Elements:</strong>
                <ul>
                    ${post.culture_elements.split(',').map(element => `
                        <li>${element}</li>
                    `).join('')}
                </ul>
            </div>
        ` : '';

        const learningStyles = post.learning_styles ? `
            <div class="learning-styles">
                <strong>Learning Styles:</strong>
                <ul>
                    ${post.learning_styles.split(',').map(style => `
                        <li>${style}</li>
                    `).join('')}
                </ul>
            </div>
        ` : '';

        let mediaContent = '';
        if (post.file_path) {
            const fileExtension = post.file_path.split('.').pop().toLowerCase();
            if (['mp4', 'webm', 'ogg'].includes(fileExtension)) {
                mediaContent = `<video controls style="width: 100%;"><source src="${post.file_path}" type="video/${fileExtension}">Your browser does not support the video tag.</video>`;
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                mediaContent = `<img src="${post.file_path}" alt="${post.title}" style="width: 100%;">`;
            }
        }

        const postHtml = `
            <div class="post" data-post-id="${post.id}">
                <div class="post-header">
                    <img src="${post.profile_picture}" alt="${post.username}" class="profile-pic">
                    <span>${post.username}</span>
                    ${(post.user_id == currentUserId || isAdmin) ? `
                        <button class="delete-post" data-post-id="${post.id}">🗑️</button>
                    ` : ''}
                </div>
                <div class="post-content">
                    <h3>${post.title}</h3>
                    <p>${post.description}</p>
                    ${mediaContent}
                </div>
                ${cultureElements}
                ${learningStyles}
                <div class="post-interactions">
                    <button class="like-btn ${post.user_liked > 0 ? 'liked' : ''}">
                        👍 ${post.like_count} Likes
                    </button>
                    <button class="comment-toggle">
                        💬 ${post.comment_count} Comments
                    </button>
                </div>
                <div class="comments-section" style="display:none;">
                    <div class="comments-list">
                        ${comments}
                    </div>
                    <div class="comment-input">
                        <input type="text" placeholder="Write a comment..." class="comment-text">
                        <button class="submit-comment">Send</button>
                    </div>
                </div>
            </div>
        `;

        return postHtml;
    }

    $(document).on('click', '.like-btn', function() {
        const postId = $(this).closest('.post').data('post-id');
        const likeBtn = $(this);

        $.ajax({
            url: 'posts_management.php',
            type: 'POST',
            data: { 
                action: 'toggle_like', 
                post_id: postId 
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'liked') {
                    likeBtn.addClass('liked');
                    const likeCount = parseInt(likeBtn.text().split(' ')[1]);
                    likeBtn.text(`👍 ${likeCount + 1} Likes`);
                } else {
                    likeBtn.removeClass('liked');
                    const likeCount = parseInt(likeBtn.text().split(' ')[1]);
                    likeBtn.text(`👍 ${likeCount - 1} Likes`);
                }
            }
        });
    });

    $(document).on('click', '.comment-toggle', function() {
        $(this).closest('.post').find('.comments-section').toggle();
    });

    $(document).on('click', '.submit-comment', function() {
        const post = $(this).closest('.post');
        const postId = post.data('post-id');
        const commentText = post.find('.comment-text').val().trim();

        if (commentText) {
            $.ajax({
                url: 'posts_management.php',
                type: 'POST',
                data: { 
                    action: 'add_comment', 
                    post_id: postId, 
                    comment_text: commentText 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        post.find('.comment-text').val('');
                        fetchPosts();
                    }
                }
            });
        }
    });

    initializeFilters();
    fetchPosts();
});
