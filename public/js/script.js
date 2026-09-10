// --- STATE APLIKASI ---
let currentUser = JSON.parse(localStorage.getItem('currentUser')) || { username: 'Guest', avatar: 'https://picsum.photos/id/1005/150/150', bio: '', link: '' };
let posts = JSON.parse(localStorage.getItem('posts')) || [];
let stories = JSON.parse(localStorage.getItem('stories')) || [];

let currentMediaType = 'image';
let currentSWType = 'image';
let selectedMediaData = null; // dataURL or objectURL for preview
let selectedMediaFile = null; // File object for upload
let selectedSWData = null;
let currentCameraStream = null;
let facingMode = 'user';
let swTimer = null;
let postsPerPage = 6;
let feedPage = 1;

// --- INITIALIZATION ---
document.addEventListener('DOMContentLoaded', () => {
  fetch('api.php?action=session_user').then(r=>r.json()).then(res=>{
    if (res.ok && res.user) { currentUser = Object.assign(currentUser, res.user); currentUser.id = res.user.id; saveData(); }
  }).finally(()=>{
    updateUserUI();
    renderStories();
    fetchPosts();
    renderProfileGrid('all');
  });
});

function fetchPosts(){
  fetch('api.php?action=get_posts').then(r=>r.json()).then(res=>{
    if (res.ok && Array.isArray(res.posts)){
      posts = res.posts.map(p=>({ id: parseInt(p.id), user_id: parseInt(p.user_id||0), username: p.username, avatar: p.avatar, type: p.type, mediaUrl: p.media_url||p.mediaUrl, caption: p.caption, likes: parseInt(p.likes||0), comments: [] }));
      saveData();
      feedPage = 1;
      renderFeedPosts();
      renderProfileGrid('all');
    } else {
      renderFeedPosts();
    }
  }).catch(()=>renderFeedPosts());
}

// --- NAVIGASI VIEW ---

// --- USER & AUTH ---
function updateUserUI() {
  document.getElementById('navUsername').textContent = currentUser.username;
  document.getElementById('displayProfileUsername').textContent = currentUser.username;
  document.getElementById('displayProfileBio').textContent = currentUser.bio;
  document.getElementById('displayProfileLink').textContent = currentUser.link;
  document.getElementById('displayProfileAvatar').src = currentUser.avatar;
}

function openLoginModal() {
  document.getElementById('loginUsernameInput').value = currentUser.username;
  document.getElementById('loginAvatarPreview').src = currentUser.avatar;
  document.getElementById('loginModal').style.display = 'flex';
}

function closeLoginModal() {
  document.getElementById('loginModal').style.display = 'none';
}

function handleLoginAvatarSelect(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      document.getElementById('loginAvatarPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function submitLogin() {
  const username = document.getElementById('loginUsernameInput').value.trim();
  const password = document.getElementById('loginPasswordInput').value || '';
  const avatar = document.getElementById('loginAvatarPreview').src;

  if (!username || !password) {
    alert('Masukkan username dan password untuk login.');
    return;
  }

  // POST ke API login
  fetch('api.php?action=login', {
    method: 'POST',
    headers: { 'Accept': 'application/json' },
    body: new URLSearchParams({ username, password })
  }).then(r=>r.json()).then(res=>{
    if (res.ok) {
      currentUser.id = res.user.id;
      currentUser.username = res.user.username;
      currentUser.avatar = res.user.avatar || avatar;
      currentUser.bio = res.user.bio || '';
      currentUser.link = res.user.link || '';
      saveData();
      updateUserUI();
      closeLoginModal();
      alert('Login berhasil');
    } else {
      alert('Login gagal: ' + (res.msg||'server error'));
    }
  }).catch(err=>{ alert('Gagal terhubung ke server: '+err.message); });
}

function googleLogin(){
  alert('Fitur Google Login memerlukan konfigurasi OAuth di server. Silakan konfigurasikan Google API dan tambahkan callback.');
}

function confirmDeleteAccount(){
  if (!confirm('Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.')) return;
  // find user id by matching username in local DB is not available; request backend with username
  // For demo, require entering user id
  const username = currentUser.username;
  fetch('api.php?action=delete_profile', {
    method: 'POST',
    headers: { 'Accept': 'application/json' },
    body: new URLSearchParams({ id: currentUser.id || 0 })
  }).then(r=>r.json()).then(res=>{
    if (res.ok) {
      alert('Akun dihapus');
      logoutUser();
    } else {
      alert('Gagal menghapus akun: ' + (res.msg||'server error'));
    }
  }).catch(err=>alert('Gagal terhubung: '+err.message));
}

function logoutUser() {
  fetch('api.php?action=logout', { method: 'POST' }).finally(()=>{
    currentUser = { username: 'Guest', avatar: 'https://picsum.photos/id/1005/150/150', bio:'', link:'' };
    delete currentUser.id; saveData(); updateUserUI(); alert('Berhasil Logout');
  });
}

// --- EDIT PROFIL ---
function openEditProfileModal() {
  document.getElementById('editUsernameInput').value = currentUser.username;
  document.getElementById('editBioInput').value = currentUser.bio;
  document.getElementById('editLinkInput').value = currentUser.link;
  document.getElementById('editAvatarPreview').src = currentUser.avatar;
  document.getElementById('editProfileModal').style.display = 'flex';
}

function closeEditProfileModal() {
  document.getElementById('editProfileModal').style.display = 'none';
}

function handleAvatarSelect(event) {
  const file = event.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      document.getElementById('editAvatarPreview').src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function saveProfileChanges() {
  const newUsername = document.getElementById('editUsernameInput').value.trim() || currentUser.username;
  const newBio = document.getElementById('editBioInput').value.trim();
  const newLink = document.getElementById('editLinkInput').value.trim();
  const newAvatar = document.getElementById('editAvatarPreview').src;
  if (currentUser.id) {
    const payload = new URLSearchParams();
    payload.append('id', currentUser.id);
    payload.append('username', newUsername);
    payload.append('bio', newBio);
    payload.append('link', newLink);
    payload.append('avatar', newAvatar);
    fetch('api.php?action=update_profile', { method: 'POST', body: payload }).then(r=>r.json()).then(res=>{
      if (res.ok && res.user){
        currentUser = Object.assign(currentUser, res.user);
        saveData();
        updateUserUI();
        closeEditProfileModal();
        alert('Profil diperbarui');
      } else alert('Gagal update: '+(res.msg||'error'));
    }).catch(err=>alert('Gagal terhubung: '+err.message));
  } else {
    currentUser.username = newUsername;
    currentUser.bio = newBio;
    currentUser.link = newLink;
    currentUser.avatar = newAvatar;
    saveData();
    updateUserUI();
    closeEditProfileModal();
  }
}

// --- KAMERA ---
async function startCamera(videoElementId) {
  stopCamera();
  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: facingMode },
      audio: false
    });
    currentCameraStream = stream;
    const videoEl = document.getElementById(videoElementId);
    videoEl.srcObject = stream;
    // ensure playback starts in browsers that require explicit play
    try { await videoEl.play(); } catch(e) { /* ignore */ }
  } catch (err) {
    alert('Gagal mengakses kamera: ' + err.message);
  }
}

function stopCamera() {
  if (currentCameraStream) {
    currentCameraStream.getTracks().forEach(track => track.stop());
    currentCameraStream = null;
  }
}

function switchCamera(videoElementId) {
  facingMode = facingMode === 'user' ? 'environment' : 'user';
  startCamera(videoElementId);
}

function captureFrame(videoEl) {
  const canvas = document.getElementById('hiddenCanvas');
  canvas.width = videoEl.videoWidth || 640;
  canvas.height = videoEl.videoHeight || 480;
  const ctx = canvas.getContext('2d');
  ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);
  return canvas.toDataURL('image/jpeg');
}

// --- UPLOAD POSTINGAN ---
function openUploadModal() {
  document.getElementById('uploadModal').style.display = 'flex';
  setMediaType('image');
}

function closeUploadModal() {
  stopCamera();
  document.getElementById('uploadModal').style.display = 'none';
  // revoke object URL if used
  if (selectedMediaData && selectedMediaData.startsWith && selectedMediaData.startsWith('blob:')) {
    try { URL.revokeObjectURL(selectedMediaData); } catch(e) { }
  }
  selectedMediaData = null;
  document.getElementById('mediaPreview').style.display = 'none';
  document.getElementById('publishForm').style.display = 'none';
  // reset upload UI
  document.getElementById('uploadProgressContainer').style.display = 'none';
  const publishBtn = document.querySelector('#publishForm .publish-btn') || document.querySelector('.publish-btn');
  if (publishBtn) publishBtn.disabled = false;
}

function setMediaType(type) {
  currentMediaType = type;
  document.querySelectorAll('#uploadModal .type-btn').forEach(b => b.classList.remove('active'));
  
  const fileWrapper = document.getElementById('fileUploadWrapper');
  const camContainer = document.getElementById('postCameraContainer');
  const fileInput = document.getElementById('fileInput');

  fileWrapper.style.display = 'none';
  camContainer.style.display = 'none';
  stopCamera();

  if (type === 'image') {
    fileWrapper.style.display = 'block';
    fileInput.accept = 'image/*';
    document.getElementById('btnTypeImage').classList.add('active');
  } else if (type === 'video') {
    fileWrapper.style.display = 'block';
    fileInput.accept = 'video/*';
    document.getElementById('btnTypeVideo').classList.add('active');
  } else if (type === 'audio') {
    fileWrapper.style.display = 'block';
    fileInput.accept = 'audio/*';
    document.getElementById('btnTypeAudio').classList.add('active');
  } else if (type === 'camera') {
    camContainer.style.display = 'block';
    document.getElementById('btnTypeCamera').classList.add('active');
    startCamera('postCameraVideo');
  }
}

function handleFileSelect(event) {
  const file = event.target.files[0];
  if (!file) return;
  selectedMediaFile = file;
  selectedMediaData = URL.createObjectURL(file);
  showMediaPreview(selectedMediaData, currentMediaType);
}

function takePostPhoto() {
  const videoEl = document.getElementById('postCameraVideo');
  selectedMediaData = captureFrame(videoEl);
  selectedMediaFile = null;
  stopCamera();
  document.getElementById('postCameraContainer').style.display = 'none';
  showMediaPreview(selectedMediaData, 'image');
}

function showMediaPreview(src, type) {
  const container = document.getElementById('mediaPreviewContainer');
  container.innerHTML = '';

  if (type === 'image' || type === 'camera') {
    container.innerHTML = `<img src="${src}" style="max-width:100%; max-height:250px; border-radius:8px;">`;
  } else if (type === 'video') {
    container.innerHTML = `<video src="${src}" controls style="max-width:100%; max-height:250px; border-radius:8px;"></video>`;
  } else if (type === 'audio') {
    container.innerHTML = `<audio src="${src}" controls style="width:100%; margin-top:10px;"></audio>`;
  }

  document.getElementById('mediaPreview').style.display = 'block';
  document.getElementById('publishForm').style.display = 'block';
}

function publishPost(){
  if (!selectedMediaData && !selectedMediaFile) { alert('Silakan pilih atau ambil media terlebih dahulu!'); return; }
  if (!currentUser.id){ alert('Silakan login terlebih dahulu untuk mengunggah.'); return; }
  const caption = document.getElementById('captionInput').value || '';
  const form = new FormData();
  form.append('user_id', currentUser.id);
  form.append('type', currentMediaType === 'camera' ? 'image' : currentMediaType);
  form.append('caption', caption);
  if (selectedMediaFile) form.append('media', selectedMediaFile, selectedMediaFile.name);
  else if (selectedMediaData && selectedMediaData.startsWith('data:')) form.append('media', dataURLtoBlob(selectedMediaData), 'capture.jpg');

  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'api.php?action=create_post');
  // disable publish button to avoid duplicate uploads
  const publishBtn = document.querySelector('#publishForm .publish-btn') || document.querySelector('#publishForm button');
  if (publishBtn) publishBtn.disabled = true;
  xhr.upload.onprogress = function(e){ if (e.lengthComputable){ const pct = Math.round(e.loaded/e.total*100); document.getElementById('uploadProgressContainer').style.display='block'; document.getElementById('uploadProgressFill').style.width = pct+'%'; document.getElementById('uploadProgressText').textContent = 'Mengunggah: '+pct+'%'; }};
  xhr.onload = function(){
    document.getElementById('uploadProgressContainer').style.display='none';
    // re-enable publish button
    if (publishBtn) publishBtn.disabled = false;
    try{ const res = JSON.parse(xhr.responseText); handleCreatePostResponse(res); }
    catch(e){ alert('Server error'); }
  };
  xhr.onerror = function(){ document.getElementById('uploadProgressContainer').style.display='none'; if (publishBtn) publishBtn.disabled = false; alert('Gagal mengunggah'); };
  xhr.send(form);
}

function dataURLtoBlob(dataurl){ const arr = dataurl.split(','); const mime = arr[0].match(/:(.*?);/)[1]; const bstr = atob(arr[1]); let n = bstr.length; const u8 = new Uint8Array(n); while(n--) u8[n] = bstr.charCodeAt(n); return new Blob([u8], {type:mime}); }

// --- RENDER FEED POSTS ---
function renderPosts(postList) {
  const container = document.getElementById('feedPostsContainer');
  container.innerHTML = '';

  if (postList.length === 0) {
    container.innerHTML = '<p style="text-align:center; padding:20px; color:#8e8e8e;">Tidak ada postingan.</p>';
    return;
  }

  postList.forEach(post => {
    let mediaHTML = '';
    if (post.type === 'image') {
      mediaHTML = `<img src="${post.mediaUrl}" class="post-media" alt="Post">`;
    } else if (post.type === 'video') {
      mediaHTML = `<video src="${post.mediaUrl}" class="post-media" controls></video>`;
    } else if (post.type === 'audio') {
      mediaHTML = `<div style="padding:20px; background:#f0f2f5; text-align:center;">
                    <i class="fa-solid fa-music fa-3x" style="color:#0095f6; margin-bottom:10px;"></i>
                    <audio src="${post.mediaUrl}" controls style="width:100%;"></audio>
                   </div>`;
    }

    const commentsHTML = post.comments.map(c => `<p class="comment-item"><b>User:</b> ${c}</p>`).join('');

    const postEl = document.createElement('article');
    postEl.className = 'post-card';
    postEl.innerHTML = `
      <div class="post-header">
        <img src="${post.avatar}" class="post-avatar" alt="${post.username}">
        <strong>${post.username}</strong>
      </div>
      <div class="post-media-box">${mediaHTML}</div>
      <div class="post-actions">
        <button onclick="toggleLike(${post.id})" style="color:${post.liked ? '#ed4956' : '#262626'}">
          <i class="${post.liked ? 'fa-solid' : 'fa-regular'} fa-heart"></i>
        </button>
        <button onclick="focusCommentInput(${post.id})"><i class="fa-regular fa-comment"></i></button>
      </div>
      <div class="post-info">
        <strong>${post.likes} menyukai</strong>
        <p><b>${post.username}</b> ${post.caption}</p>
        <div class="comments-list">${commentsHTML}</div>
        <div class="add-comment-box">
          <input type="text" id="comment-input-${post.id}" placeholder="Tambah komentar..." onkeypress="handleCommentKeyPress(event, ${post.id})">
          <button onclick="addComment(${post.id})">Kirim</button>
        </div>
      </div>
    `;
    container.appendChild(postEl);
  });
}

function toggleLike(postId){
  const post = posts.find(p => p.id === postId);
  if (!post) return;
  post.liked = !post.liked;
  post.likes = (post.likes || 0) + (post.liked ? 1 : -1);
  saveData();
  renderFeedPosts();
}

function handleCreatePostResponse(res){
  if (res.ok && res.post){
    const p = res.post;
    const newPost = {
      id: parseInt(p.id),
      user_id: parseInt(p.user_id || p.userId || 0),
      username: p.username,
      avatar: p.avatar,
      type: p.type,
      mediaUrl: p.media_url || p.mediaUrl,
      caption: p.caption,
      likes: parseInt(p.likes||0),
      comments: []
    };
    posts.unshift(newPost);
    saveData();
    feedPage = 1;
    renderFeedPosts();
    renderProfileGrid('all');
    closeUploadModal();
    document.getElementById('captionInput').value = '';
    selectedMediaFile = null;
    selectedMediaData = null;
    // ensure publish button enabled
    const publishBtn = document.querySelector('#publishForm .publish-btn') || document.querySelector('#publishForm button');
    if (publishBtn) publishBtn.disabled = false;
  } else alert('Gagal membuat post: '+(res.msg||'error'));
}

function renderFeedPosts(){
  const start = (feedPage-1)*postsPerPage;
  const slice = posts.slice(start, start+postsPerPage);
  renderPosts(slice);
  const loadMoreBtn = document.getElementById('loadMoreBtn');
  if ((start+postsPerPage) >= posts.length) loadMoreBtn.style.display='none'; else loadMoreBtn.style.display='inline-block';
}

function loadMoreFeed(){ feedPage++; renderFeedPosts(); }
function performSearch(query){
  const clearBtn = document.getElementById('clearSearchBtn');
  const resultsContainer = document.getElementById('searchResultsContainer');

  if (!query || query.length === 0) {
    if (clearBtn) clearBtn.style.display = 'none';
    if (resultsContainer) resultsContainer.innerHTML = '';
    return;
  }
  if (clearBtn) clearBtn.style.display = 'block';

  const filtered = posts.filter(p => 
    (p.username||'').toLowerCase().includes(query) ||
    (p.caption||'').toLowerCase().includes(query) ||
    (p.type||'').toLowerCase().includes(query)
  );

  resultsContainer.innerHTML = '';
  if (filtered.length === 0) {
    resultsContainer.innerHTML = '<p style="text-align:center; color:#8e8e8e;">Hasil tidak ditemukan</p>';
    return;
  }

  filtered.forEach(post => {
    const item = document.createElement('div');
    item.className = 'search-result-item';
    item.onclick = () => { switchView('feed'); renderPosts([post]); };
    item.innerHTML = `
      <img src="${post.avatar}" style="width:40px; height:40px; border-radius:50%; object-fit:cover;">
      <div>
        <strong>${post.username}</strong>
        <p style="font-size:0.8rem; color:#8e8e8e;">${(post.caption||'').substring(0, 30)}... (${post.type})</p>
      </div>
    `;
    resultsContainer.appendChild(item);
  });
  
}

function clearSearch() {
  document.getElementById('searchInput').value = '';
  document.getElementById('clearSearchBtn').style.display = 'none';
  document.getElementById('searchResultsContainer').innerHTML = '';
}

// --- STORIES & WHATSAPP STATUS ---
function renderStories() {
  const container = document.getElementById('storiesContainer');
  container.innerHTML = '';

  // Button tambah status
  const addBtn = document.createElement('div');
  addBtn.className = 'story-item';
  addBtn.onclick = openAddSWModal;
  addBtn.innerHTML = `
    <div class="story-avatar-wrapper add-story">
      <img src="${currentUser.avatar}" alt="My Avatar">
      <span class="add-icon">+</span>
    </div>
    <span class="story-username">Status Saya</span>
  `;
  container.appendChild(addBtn);

  stories.forEach(sw => {
    const swItem = document.createElement('div');
    swItem.className = 'story-item';
    swItem.onclick = () => openSWModal(sw);
    swItem.innerHTML = `
      <div class="story-avatar-wrapper">
        <img src="${sw.avatar}" alt="${sw.username}">
      </div>
      <span class="story-username">${sw.username}</span>
    `;
    container.appendChild(swItem);
  });
}

function openAddSWModal() {
  document.getElementById('addSWModal').style.display = 'flex';
  setSWType('image');
}

function closeAddSWModal() {
  stopCamera();
  document.getElementById('addSWModal').style.display = 'none';
  selectedSWData = null;
}

function setSWType(type) {
  currentSWType = type;
  document.querySelectorAll('#addSWModal .type-btn').forEach(b => b.classList.remove('active'));

  const fileInput = document.getElementById('swFileInputContainer');
  const camContainer = document.getElementById('swCameraContainer');
  const textContainer = document.getElementById('swTextContainer');

  fileInput.style.display = 'none';
  camContainer.style.display = 'none';
  textContainer.style.display = 'none';
  stopCamera();

  if (type === 'image') {
    fileInput.style.display = 'block';
    document.getElementById('btnSWImage').classList.add('active');
  } else if (type === 'camera') {
    camContainer.style.display = 'block';
    document.getElementById('btnSWCamera').classList.add('active');
    startCamera('swCameraVideo');
  } else if (type === 'text') {
    textContainer.style.display = 'block';
    document.getElementById('btnSWText').classList.add('active');
  }
}

function handleSWFileSelect(e) {
  const file = e.target.files[0];
  if (!file) return;
  const reader = new FileReader();
  reader.onload = (evt) => {
    selectedSWData = evt.target.result;
  };
  reader.readAsDataURL(file);
}

function takeSWPhoto() {
  const videoEl = document.getElementById('swCameraVideo');
  selectedSWData = captureFrame(videoEl);
  stopCamera();
  alert('Foto berhasil ditangkap!');
}

function publishSW() {
  const caption = document.getElementById('swCaptionInput').value;
  let textVal = '';

  if (currentSWType === 'text') {
    textVal = document.getElementById('swTextInput').value;
    if (!textVal) {
      alert('Tuliskan teks status Anda!');
      return;
    }
  } else if (!selectedSWData) {
    alert('Silakan pilih/sediakan media foto terlebih dahulu!');
    return;
  }

  const newSW = {
    id: Date.now(),
    username: currentUser.username,
    avatar: currentUser.avatar,
    type: currentSWType,
    mediaUrl: selectedSWData || '',
    text: textVal,
    caption: caption,
    time: 'Baru saja'
  };

  stories.unshift(newSW);
  saveData();
  renderStories();
  closeAddSWModal();
  document.getElementById('swCaptionInput').value = '';
  document.getElementById('swTextInput').value = '';
}

function openSWModal(sw) {
  const modal = document.getElementById('swModal');
  const avatar = document.getElementById('swUserAvatar');
  const username = document.getElementById('swUsername');
  const time = document.getElementById('swTime');
  const body = document.getElementById('swBodyContent');
  const progress = document.getElementById('swProgressFill');

  avatar.src = sw.avatar;
  username.textContent = sw.username;
  time.textContent = sw.time;
  body.innerHTML = '';

  if (sw.type === 'text') {
    body.innerHTML = `<div style="background:#075e54; width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#fff; font-size:1.5rem; padding:20px; text-align:center;">${sw.text}</div>`;
  } else {
    body.innerHTML = `<img src="${sw.mediaUrl}" style="max-width:100%; max-height:80vh; object-fit:contain;">
                      ${sw.caption ? `<p style="color:#fff; margin-top:10px; text-align:center;">${sw.caption}</p>` : ''}`;
  }

  modal.style.display = 'flex';
  
  // Progress Bar Animation
  progress.style.width = '0%';
  setTimeout(() => progress.style.width = '100%', 50);

  clearTimeout(swTimer);
  swTimer = setTimeout(() => {
    closeSWModal();
  }, 5000);
}

function closeSWModal() {
  clearTimeout(swTimer);
  document.getElementById('swModal').style.display = 'none';
}

// --- PROFILE GALLERY & FILTER ---
function filterProfileGallery(type) {
  document.querySelectorAll('.profile-tab-btn').forEach(btn => btn.classList.remove('active'));
  
  if (type === 'all') document.getElementById('tabAll').classList.add('active');
  if (type === 'image') document.getElementById('tabImage').classList.add('active');
  if (type === 'video') document.getElementById('tabVideo').classList.add('active');
  if (type === 'audio') document.getElementById('tabAudio').classList.add('active');

  renderProfileGrid(type);
}

function renderProfileGrid(filter) {
  const container = document.getElementById('profileGridContainer');
  const myPosts = posts.filter(p => p.username === currentUser.username);
  
  document.getElementById('profilePostCount').textContent = myPosts.length;
  container.innerHTML = '';

  const filteredPosts = filter === 'all' ? myPosts : myPosts.filter(p => p.type === filter);

  if (filteredPosts.length === 0) {
    container.innerHTML = '<p style="grid-column: 1/-1; text-align:center; padding:20px; color:#8e8e8e;">Belum ada postingan</p>';
    return;
  }
  filteredPosts.forEach(post => {
    const item = document.createElement('div');
    item.className = 'grid-item';
    let mediaHTML = '';
    if (post.type === 'image') mediaHTML = `<img src="${post.mediaUrl}" alt="post">`;
    else if (post.type === 'video') mediaHTML = `<video src="${post.mediaUrl}" controls></video>`;
    else mediaHTML = `<div style="display:flex;align-items:center;justify-content:center;height:100%;">${post.type}</div>`;

    const isOwner = post.user_id && currentUser.id && parseInt(post.user_id) === parseInt(currentUser.id);
    const actions = isOwner ? `\n      <div style="position:absolute; top:6px; right:6px; display:flex; gap:6px;">\n        <button class="action-btn" onclick="editPost(${post.id})" title="Edit">✏️</button>\n        <button class="action-btn" onclick="deletePost(${post.id})" title="Hapus" style="color:#c0392b;">🗑️</button>\n      </div>\n    ` : '';

    item.innerHTML = `\n      ${mediaHTML}\n      ${actions}\n    `;

    item.onclick = (e) => {
      if (e.target.closest('.action-btn')) return;
      switchView('feed');
      renderPosts([post]);
    };

    container.appendChild(item);
  });
}

function editPost(postId){
  const post = posts.find(p=>p.id === postId);
  if (!post) return alert('Postingan tidak ditemukan');
  const newCaption = prompt('Edit caption:', post.caption || '');
  if (newCaption === null) return;
  fetch('api.php?action=update_post', { method: 'POST', body: new URLSearchParams({ id: postId, caption: newCaption }) }).then(r=>r.json()).then(res=>{
    if (res.ok){
      post.caption = newCaption;
      saveData();
      renderPosts(posts);
      renderProfileGrid('all');
      alert('Diupdate');
    } else alert('Gagal update: '+(res.msg||'error'));
  }).catch(err=>alert('Gagal terhubung: '+err.message));
}

function deletePost(postId){
  if (!confirm('Hapus postingan ini?')) return;
  fetch('api.php?action=delete_post', { method: 'POST', body: new URLSearchParams({ id: postId }) }).then(r=>r.json()).then(res=>{
    if (res.ok){
      posts = posts.filter(p=>p.id !== postId);
      saveData();
      renderPosts(posts);
      renderProfileGrid('all');
      alert('Dihapus');
    } else alert('Gagal hapus: '+(res.msg||'error'));
  }).catch(err=>alert('Gagal terhubung: '+err.message));
}

// Quick camera capture: open camera modal and take a photo immediately
function openCameraAndCapture(){
  openUploadModal();
  setMediaType('camera');
  // wait a bit for camera to initialize then capture
  setTimeout(()=>{
    const videoEl = document.getElementById('postCameraVideo');
    if (videoEl && videoEl.readyState >= 2) {
      takePostPhoto();
    } else {
      // try again shortly
      setTimeout(()=>{ if (document.getElementById('postCameraVideo')) takePostPhoto(); }, 700);
    }
  }, 700);
}