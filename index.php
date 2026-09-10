<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>InstaClone Pro - Feed, Search, Camera & WA Status</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

  <header class="navbar">
    <h1 class="logo">InstaClone</h1>
    <div class="nav-icons">
      <div class="login-status-badge" onclick="openLoginModal()" id="navUserBadge" role="button">
        <i class="fa-solid fa-user"></i> <span id="navUsername">Login</span>
      </div>
      <button type="button" class="nav-btn" onclick="openUploadModal()" title="Tambah Postingan">
        <i class="fa-regular fa-square-plus"></i>
      </button>
    </div>
  </header>

  <section id="feedView" class="view-section active">
    <div class="stories-container" id="storiesContainer"></div>
    <div id="feedPostsContainer"></div>
      <div id="loadMoreContainer" style="text-align:center; margin:12px 0;">
        <button id="loadMoreBtn" onclick="loadMoreFeed()" style="background:#fff; border:1px solid #dbdbdb; padding:8px 12px; border-radius:8px; cursor:pointer;">Muat lebih</button>
      </div>
  </section>

  <section id="searchView" class="view-section">
    <div class="search-box-container">
      <div class="search-input-wrapper" style="position: relative; display: flex; align-items: center;">
        <i class="fa-solid fa-magnifying-glass" style="color: #8e8e8e; position: absolute; left: 12px;"></i>
        <input type="text" id="searchInput" placeholder="Cari postingan, username, atau tipe..." oninput="handleSearch()" autocomplete="off" style="padding-left: 35px; padding-right: 35px; width: 100%;">
        <i class="fa-solid fa-xmark" id="clearSearchBtn" onclick="clearSearch()" style="color: #8e8e8e; position: absolute; right: 12px; cursor: pointer; display: none;"></i>
      </div>
    </div>
    <div id="searchResultsContainer" style="padding: 12px 0;"></div>
  </section>

  <section id="profileView" class="view-section">
    <div class="profile-header">
      <div class="profile-top">
        <img id="displayProfileAvatar" src="https://picsum.photos/id/1005/150/150" class="profile-avatar" alt="Profile Avatar">
        <div class="profile-stats">
          <div>
            <div class="stat-number" id="profilePostCount">0</div>
            <div class="stat-label">Postingan</div>
          </div>
          <div>
            <div class="stat-number">1,240</div>
            <div class="stat-label">Pengikut</div>
          </div>
          <div>
            <div class="stat-number">350</div>
            <div class="stat-label">Mengikuti</div>
          </div>
        </div>
      </div>
      <div class="profile-bio">
        <strong id="displayProfileUsername">user_kamu</strong>
        <p id="displayProfileBio">Fullstack Web Developer & Content Creator 🚀✨</p>
        <p id="displayProfileLink" style="color: #00376b;">linktr.ee/karyasaya</p>
      </div>
      <button type="button" class="edit-profile-btn" onclick="openEditProfileModal()">Edit Profil</button>
      <button type="button" class="edit-profile-btn" onclick="logoutUser()" style="background:#ffebe9; color:#ed4956; margin-top:6px;">Logout</button>
      <button type="button" class="edit-profile-btn" onclick="confirmDeleteAccount()" style="background:#ffe6e6; color:#c0392b; margin-top:6px;">Hapus Akun</button>
    </div>

    <div class="profile-tabs">
      <button type="button" class="profile-tab-btn active" id="tabAll" onclick="filterProfileGallery('all')"><i class="fa-solid fa-border-all"></i></button>
      <button type="button" class="profile-tab-btn" id="tabImage" onclick="filterProfileGallery('image')"><i class="fa-regular fa-image"></i></button>
      <button type="button" class="profile-tab-btn" id="tabVideo" onclick="filterProfileGallery('video')"><i class="fa-solid fa-film"></i></button>
      <button type="button" class="profile-tab-btn" id="tabAudio" onclick="filterProfileGallery('audio')"><i class="fa-solid fa-music"></i></button>
    </div>

    <div class="profile-grid" id="profileGridContainer"></div>
  </section>

  <nav class="bottom-nav">
    <button type="button" class="nav-tab active" onclick="switchView('feed')"><i class="fa-solid fa-house"></i></button>
    <button type="button" class="nav-tab" onclick="switchView('search')"><i class="fa-solid fa-magnifying-glass"></i></button>
    <button type="button" class="nav-tab" onclick="openUploadModal()"><i class="fa-regular fa-square-plus"></i></button>
    <button type="button" class="nav-tab" onclick="switchView('profile')"><i class="fa-regular fa-circle-user"></i></button>
  </nav>

  <!-- Quick camera capture button -->
  <button id="quickCamBtn" onclick="openCameraAndCapture()" title="Foto Cepat" style="position:fixed; right:16px; bottom:86px; background:#000; color:#fff; border-radius:50%; width:56px; height:56px; display:flex; align-items:center; justify-content:center; font-size:1.2rem; z-index:30; border:none; box-shadow:0 4px 12px rgba(0,0,0,0.15);"> 
    <i class="fa-solid fa-camera"></i>
  </button>

  <div class="sw-modal" id="swModal">
    <div class="sw-header">
      <div class="sw-progress-bar">
        <div class="sw-progress-fill" id="swProgressFill"></div>
      </div>
      <div class="sw-user-info">
        <div class="sw-user-details">
          <img id="swUserAvatar" class="sw-avatar" src="" alt="SW User">
          <div>
            <strong id="swUsername" style="font-size:0.9rem;"></strong>
            <div id="swTime" style="font-size:0.7rem; color:#aaa;">Baru saja</div>
          </div>
        </div>
        <button type="button" onclick="closeSWModal()" style="background:none; border:none; color:#fff; font-size:1.4rem; cursor:pointer;">&times;</button>
      </div>
    </div>
    <div class="sw-body" id="swBodyContent"></div>
  </div>

  <div class="modal" id="addSWModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Buat Status WA Baru</h3>
        <button type="button" class="close-btn" onclick="closeAddSWModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="type-btn-group">
          <button type="button" class="type-btn active" id="btnSWImage" onclick="setSWType('image')">Foto / File</button>
          <button type="button" class="type-btn" id="btnSWCamera" onclick="setSWType('camera')">Kamera Langsung</button>
          <button type="button" class="type-btn" id="btnSWText" onclick="setSWType('text')">Status Teks</button>
        </div>

        <div id="swFileInputContainer">
          <input type="file" id="swFileInput" accept="image/*" onchange="handleSWFileSelect(event)">
        </div>

        <div id="swCameraContainer" class="camera-container" style="display:none;">
          <video id="swCameraVideo" class="camera-video" autoplay playsinline muted></video>
          <div style="display: flex; gap: 8px; margin-top: 8px;">
            <button type="button" class="camera-btn" onclick="takeSWPhoto()"><i class="fa-solid fa-camera"></i> Ambil Foto</button>
            <button type="button" class="camera-btn" onclick="switchCamera('swCameraVideo')" style="background: #555;"><i class="fa-solid fa-rotate"></i> Putar Kamera</button>
          </div>
        </div>

        <div id="swTextContainer" style="display:none;">
          <textarea id="swTextInput" class="form-input" style="background:#075e54; color:#fff;" placeholder="Ketik status WhatsApp Anda..."></textarea>
        </div>

        <input type="text" id="swCaptionInput" class="form-input" placeholder="Tambah keterangan status (opsional)..." autocomplete="off">
        <button type="button" class="publish-btn" onclick="publishSW()" style="background:#25D366;">Bagikan ke Status</button>
      </div>
    </div>
  </div>

  <div class="modal" id="loginModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Masuk Ke Akun</h3>
        <button type="button" class="close-btn" onclick="closeLoginModal()">&times;</button>
      </div>
      <div class="modal-body">
        <img id="loginAvatarPreview" src="https://picsum.photos/id/1005/150/150" class="avatar-preview" alt="Login Avatar">
        
        <label style="font-size: 0.8rem; font-weight:600;">Username:</label>
        <input type="text" id="loginUsernameInput" class="form-input" placeholder="Masukkan username Anda..." autocomplete="off">

        <label style="font-size: 0.8rem; font-weight:600; margin-top:8px;">Password:</label>
        <input type="password" id="loginPasswordInput" class="form-input" placeholder="Masukkan password Anda..." autocomplete="off">

        <label style="font-size: 0.8rem; font-weight:600;">Pilih Foto Profil (Opsional):</label>
        <input type="file" id="loginAvatarFileInput" accept="image/*" onchange="handleLoginAvatarSelect(event)">

        <div style="display:flex; gap:8px; flex-direction:column;">
          <button type="button" class="publish-btn" onclick="submitLogin()">Masuk / Hubungkan</button>
          <button type="button" class="publish-btn" onclick="googleLogin()" style="background:#4285F4;">Masuk dengan Google</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" id="uploadModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Postingan Baru</h3>
        <button type="button" class="close-btn" onclick="closeUploadModal()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="type-btn-group">
          <button type="button" class="type-btn active" id="btnTypeImage" onclick="setMediaType('image')">Upload File</button>
          <button type="button" class="type-btn" id="btnTypeCamera" onclick="setMediaType('camera')">📸 Foto Kamera</button>
          <button type="button" class="type-btn" id="btnTypeVideo" onclick="setMediaType('video')">Video</button>
          <button type="button" class="type-btn" id="btnTypeAudio" onclick="setMediaType('audio')">Audio</button>
        </div>

        <div id="fileUploadWrapper">
          <input type="file" id="fileInput" accept="image/*" onchange="handleFileSelect(event)">
        </div>

        <div id="postCameraContainer" class="camera-container" style="display:none;">
          <video id="postCameraVideo" class="camera-video" autoplay playsinline muted></video>
          <div style="display: flex; gap: 8px; margin-top: 8px;">
            <button type="button" class="camera-btn" onclick="takePostPhoto()"><i class="fa-solid fa-camera"></i> Jepret Foto</button>
            <button type="button" class="camera-btn" onclick="switchCamera('postCameraVideo')" style="background: #555;"><i class="fa-solid fa-rotate"></i> Putar Kamera</button>
          </div>
        </div>

        <div id="mediaPreview" style="display: none;">
          <div id="mediaPreviewContainer"></div>
        </div>

        <div id="publishForm" style="display: none;">
          <textarea id="captionInput" class="form-input" placeholder="Tulis caption menarik..."></textarea>
          <button type="button" class="publish-btn" onclick="publishPost()">Bagikan</button>
        </div>
        <div id="uploadProgressContainer" style="display:none; margin-top:8px;">
          <div style="background:#efefef; border-radius:6px; overflow:hidden; height:10px;">
            <div id="uploadProgressFill" style="width:0%; height:100%; background:#4caf50;"></div>
          </div>
          <div id="uploadProgressText" style="font-size:0.8rem; color:#666; margin-top:6px;">Mengunggah...</div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal" id="editProfileModal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Edit Profil</h3>
        <button type="button" class="close-btn" onclick="closeEditProfileModal()">&times;</button>
      </div>
      <div class="modal-body">
        <img id="editAvatarPreview" src="" class="avatar-preview" alt="Preview Profil">
        
        <label style="font-size: 0.8rem; font-weight:600;">Ganti Foto Profil:</label>
        <input type="file" id="avatarFileInput" accept="image/*" onchange="handleAvatarSelect(event)">

        <label style="font-size: 0.8rem; font-weight:600;">Username:</label>
        <input type="text" id="editUsernameInput" class="form-input" placeholder="Username" autocomplete="off">

        <label style="font-size: 0.8rem; font-weight:600;">Bio:</label>
        <textarea id="editBioInput" class="form-input" placeholder="Tulis bio profil..."></textarea>

        <label style="font-size: 0.8rem; font-weight:600;">Link Website:</label>
        <input type="text" id="editLinkInput" class="form-input" placeholder="linktr.ee/kamu" autocomplete="off">

        <button type="button" class="publish-btn" onclick="saveProfileChanges()">Simpan Perubahan</button>
      </div>
    </div>
  </div>

  <canvas id="hiddenCanvas" style="display:none;"></canvas>

  <script src="public/js/script.js"></script>
</body>
</html>