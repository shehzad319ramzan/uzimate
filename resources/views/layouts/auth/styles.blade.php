<link href="{{ asset('dashboard/css/modern.css') }}" rel="stylesheet">
{{-- <link href="{{ asset('dashboard/css/classic.css') }}" rel="stylesheet">
<link href="{{ asset('dashboard/css/dark.css') }}" rel="stylesheet">
<link href="{{ asset('dashboard/css/light.css') }}" rel="stylesheet"> --}}
<link href="{{ asset('dashboard/css/custom.css') }}" rel="stylesheet">
<style>
  /* Critical inline overrides for sidebar visibility - Highest priority */
  #sidebar,
  nav#sidebar {
    background-color: #202938 !important;
    background: #202938 !important;
  }
  
  /* Override modern.css .sidebar-nav background: #fff */
  #sidebar .sidebar-nav,
  nav#sidebar .sidebar-nav,
  .sidebar .sidebar-nav,
  .sidebar-nav {
    background: transparent !important;
    background-color: transparent !important;
  }
  
  #sidebar .sidebar-link,
  #sidebar a.sidebar-link,
  nav#sidebar .sidebar-link,
  nav#sidebar a.sidebar-link {
    color: rgba(255, 255, 255, 0.9) !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
  }
  
  #sidebar .sidebar-link i,
  #sidebar .sidebar-link .fas,
  #sidebar .sidebar-link .far,
  #sidebar .sidebar-link .fab,
  #sidebar .sidebar-link .fal,
  #sidebar .sidebar-link svg,
  nav#sidebar .sidebar-link i,
  nav#sidebar .sidebar-link .fas,
  nav#sidebar .sidebar-link .far,
  nav#sidebar .sidebar-link .fab,
  nav#sidebar .sidebar-link .fal,
  nav#sidebar .sidebar-link svg {
    color: rgba(255, 255, 255, 0.8) !important;
    display: inline-block !important;
    visibility: visible !important;
  }
  
  #sidebar .sidebar-link span,
  #sidebar a.sidebar-link span,
  #sidebar .sidebar-link .align-middle,
  nav#sidebar .sidebar-link span,
  nav#sidebar a.sidebar-link span,
  nav#sidebar .sidebar-link .align-middle {
    color: rgba(255, 255, 255, 0.9) !important;
    display: inline !important;
    visibility: visible !important;
  }
  
  #sidebar .sidebar-dropdown .sidebar-link,
  #sidebar .sidebar-dropdown a.sidebar-link,
  nav#sidebar .sidebar-dropdown .sidebar-link,
  nav#sidebar .sidebar-dropdown a.sidebar-link {
    color: rgba(255, 255, 255, 0.9) !important;
    background: transparent !important;
    background-color: transparent !important;
    display: block !important;
    visibility: visible !important;
  }
  
  #sidebar .sidebar-item,
  #sidebar li,
  nav#sidebar .sidebar-item,
  nav#sidebar li {
    display: list-item !important;
    visibility: visible !important;
    opacity: 1 !important;
  }
  
  #sidebar .sidebar-nav,
  nav#sidebar .sidebar-nav {
    display: flex !important;
    flex-direction: column !important;
    visibility: visible !important;
    background: transparent !important;
    background-color: transparent !important;
  }
  
  /* Force all text inside sidebar to be white - but exclude logo icons */
  #sidebar .sidebar-link,
  #sidebar .sidebar-link span,
  #sidebar .sidebar-header,
  #sidebar .sidebar-item,
  #sidebar .user-role,
  #sidebar .user-email,
  nav#sidebar .sidebar-link,
  nav#sidebar .sidebar-link span,
  nav#sidebar .sidebar-header,
  nav#sidebar .sidebar-item,
  nav#sidebar .user-role,
  nav#sidebar .user-email {
    color: rgba(255, 255, 255, 0.9) !important;
  }
  
  #sidebar .sidebar-header,
  nav#sidebar .sidebar-header {
    color: rgba(255, 255, 255, 0.6) !important;
  }
  
  /* Override all icon colors */
  #sidebar i,
  #sidebar .fas,
  #sidebar .far,
  #sidebar .fab,
  #sidebar .fal,
  #sidebar svg,
  nav#sidebar i,
  nav#sidebar .fas,
  nav#sidebar .far,
  nav#sidebar .fab,
  nav#sidebar .fal,
  nav#sidebar svg {
    color: rgba(255, 255, 255, 0.8) !important;
  }
  
  /* Override hover states */
  #sidebar .sidebar-link:hover,
  nav#sidebar .sidebar-link:hover {
    background-color: rgba(255, 255, 255, 0.1) !important;
    color: #FFFFFF !important;
  }
  
  #sidebar .sidebar-link:hover i,
  nav#sidebar .sidebar-link:hover i {
    color: #FFFFFF !important;
  }
  
  /* Advanced Mode Items - Force hide/show */
  #sidebar .advance-mode-item:not(.show),
  nav#sidebar .advance-mode-item:not(.show) {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    overflow: hidden !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  
  #sidebar .advance-mode-item.show,
  nav#sidebar .advance-mode-item.show {
    display: list-item !important;
    visibility: visible !important;
    opacity: 1 !important;
    height: auto !important;
    overflow: visible !important;
  }
  .message {
    margin: 6px 0;
    padding: 10px 14px;
    border-radius: 8px;
    max-width: 80%;
    word-break: break-word;
  }
  .user {
    background-color: #d1e7dd;
    align-self: flex-end;
  }
  .bot {
    background-color: #f8d7da;
    align-self: flex-start;
  }
  .typing {
    display: flex;
    gap: 4px;
    padding: 8px;
    align-self: flex-start;
  }
  .dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #aaa;
    animation: blink 1s infinite;
  }
  .dot:nth-child(2) { animation-delay: 0.2s; }
  .dot:nth-child(3) { animation-delay: 0.4s; }

  @keyframes blink {
    0%, 80%, 100% { opacity: 0; }
    40% { opacity: 1; }
  }

  .suggestion-bubble {
    padding: 10px 15px;
    background-color: #e0f0ff;
    border: 2px solid #174b79;
    border-radius: 20px 5px 20px 5px;
    cursor: pointer;
    max-width: 80%;
    align-self: flex-start;
    transition: 0.3s;
  }

  .suggestion-bubble:hover {
    background-color: #d0e8ff;
  }
</style>
