(function () {
  const config = window.messagesPageConfig;
  if (!config || !Array.isArray(config.conversations)) {
    return;
  }

  const conversations = config.conversations;
  const headerRoot = document.querySelector(".chat-header-wrap");
  const messagesRoot = document.querySelector(".messages");
  const chatBox = document.querySelector(".chat-box");
  const chatPanel = document.querySelector(".chat-width");
  const sidebar = document.querySelector(".sidebar");
  const messagesTitle = document.querySelector(".messages-title");
  const settingsArchived = document.querySelector(".settings-archived");
  const exitArchivedBtn = document.querySelector(".exit-archived");
  const chatListsByTab = {
    "groomer-messages": document.querySelector(
      '[data-tab-content="groomer-messages"] .chat-list',
    ),
    "space-messages": document.querySelector(
      '[data-tab-content="space-messages"] .chat-list',
    ),
  };

  if (!headerRoot || !messagesRoot || !chatBox) {
    return;
  }

  let activeConversationId = null;
  let showingArchived = false;
  let activeFilter = null;

  const filterCheckSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
    </svg>`;

  const sendSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
        <path d="M18.2448 0.0763204C19.2868 -0.287756 20.2878 0.713198 19.9237 1.75518L13.8471 19.118C13.4522 20.2441 11.8831 20.3077 11.399 19.2175L8.46685 12.6211L12.5938 8.49316C12.7297 8.34735 12.8036 8.15449 12.8001 7.95522C12.7966 7.75595 12.7159 7.56583 12.575 7.4249C12.434 7.28398 12.2439 7.20325 12.0446 7.19974C11.8454 7.19622 11.6525 7.27019 11.5067 7.40605L7.3787 11.5329L0.782123 8.60084C-0.308075 8.11575 -0.243464 6.54765 0.881605 6.15281L18.2448 0.0763204Z" fill="white" />
    </svg>`;

  const archiveIconPath =
    'M5.958 10.2838C6.3485 10.6742 6.9815 10.6742 7.372 10.2838L9.12598 8.53034C9.2889 8.36748 9.28892 8.10338 9.12603 7.94049C8.96316 7.77762 8.69909 7.77762 8.53622 7.94049L7.08156 9.39515V5.89603C7.08156 5.66596 6.89506 5.47946 6.665 5.47946C6.43494 5.47946 6.24844 5.66596 6.24844 5.89603V9.39515L4.79378 7.94049C4.63091 7.77762 4.36684 7.77762 4.20397 7.94049C4.04108 8.10338 4.0411 8.36748 4.20402 8.53034L5.958 10.2838ZM0.833125 3.17254V11.9845C0.833125 12.1339 0.881168 12.2567 0.977256 12.3527C1.07334 12.4488 1.19637 12.4969 1.34633 12.4969H11.9845C12.1339 12.4969 12.2567 12.4488 12.3527 12.3527C12.4488 12.2567 12.4969 12.1339 12.4969 11.9845V3.17254H0.833125ZM1.47463 13.33C1.10139 13.33 0.762309 13.1775 0.457385 12.8726C0.152462 12.5677 0 12.2289 0 11.8562V2.90427C0 2.74209 0.0258268 2.58935 0.0774806 2.44605C0.129134 2.30276 0.206893 2.17085 0.310756 2.05032L1.60876 0.492377C1.72929 0.32964 1.88009 0.206893 2.06115 0.124136C2.24222 0.0413787 2.43634 0 2.64351 0H10.6548C10.8615 0 11.0581 0.0413787 11.2447 0.124136C11.4313 0.206893 11.5849 0.329362 11.7054 0.491544L13.0192 2.08281C13.1231 2.20334 13.2009 2.33803 13.2525 2.48688C13.3042 2.63517 13.33 2.79069 13.33 2.95343V11.8554C13.33 12.2281 13.1775 12.5669 12.8726 12.8718C12.5677 13.1767 12.2289 13.3292 11.8562 13.3292L1.47463 13.33ZM1.14971 2.33942H12.1636L11.0556 1.00808C11.0017 0.954761 10.94 0.912272 10.8706 0.880613C10.8012 0.848954 10.729 0.833125 10.654 0.833125H2.65933C2.58491 0.833125 2.51271 0.849232 2.44272 0.881446C2.37274 0.91366 2.31164 0.956427 2.25943 1.00975L1.14971 2.33942Z';

  // Same archive box with the arrow flipped upward (unarchive).
  const unarchiveIconPath =
    'M5.958 3.7162C6.3485 3.3258 6.9815 3.3258 7.372 3.7162L9.12598 5.46966C9.2889 5.63252 9.28892 5.89662 9.12603 6.05951C8.96316 6.22238 8.69909 6.22238 8.53622 6.05951L7.08156 4.60485V8.10397C7.08156 8.33404 6.89506 8.52054 6.665 8.52054C6.43494 8.52054 6.24844 8.33404 6.24844 8.10397V4.60485L4.79378 6.05951C4.63091 6.22238 4.36684 6.22238 4.20397 6.05951C4.04108 5.89662 4.0411 5.63252 4.20402 5.46966L5.958 3.7162ZM0.833125 3.17254V11.9845C0.833125 12.1339 0.881168 12.2567 0.977256 12.3527C1.07334 12.4488 1.19637 12.4969 1.34633 12.4969H11.9845C12.1339 12.4969 12.2567 12.4488 12.3527 12.3527C12.4488 12.2567 12.4969 12.1339 12.4969 11.9845V3.17254H0.833125ZM1.47463 13.33C1.10139 13.33 0.762309 13.1775 0.457385 12.8726C0.152462 12.5677 0 12.2289 0 11.8562V2.90427C0 2.74209 0.0258268 2.58935 0.0774806 2.44605C0.129134 2.30276 0.206893 2.17085 0.310756 2.05032L1.60876 0.492377C1.72929 0.32964 1.88009 0.206893 2.06115 0.124136C2.24222 0.0413787 2.43634 0 2.64351 0H10.6548C10.8615 0 11.0581 0.0413787 11.2447 0.124136C11.4313 0.206893 11.5849 0.329362 11.7054 0.491544L13.0192 2.08281C13.1231 2.20334 13.2009 2.33803 13.2525 2.48688C13.3042 2.63517 13.33 2.79069 13.33 2.95343V11.8554C13.33 12.2281 13.1775 12.5669 12.8726 12.8718C12.5677 13.1767 12.2289 13.3292 11.8562 13.3292L1.47463 13.33ZM1.14971 2.33942H12.1636L11.0556 1.00808C11.0017 0.954761 10.94 0.912272 10.8706 0.880613C10.8012 0.848954 10.729 0.833125 10.654 0.833125H2.65933C2.58491 0.833125 2.51271 0.849232 2.44272 0.881446C2.37274 0.91366 2.31164 0.956427 2.25943 1.00975L1.14971 2.33942Z';

  const attachmentActions = `
        <div class="actions-row">
            <label class="action dark-color-font">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12" fill="none">
                    <path d="M10.5 6.04469L6.17551 10.5107C5.54818 11.1481 4.70239 11.5037 3.82235 11.5C2.94232 11.4963 2.09936 11.1336 1.47707 10.4909C0.854792 9.8483 0.503611 8.97775 0.500028 8.06891C0.496444 7.16008 0.840748 6.2866 1.45794 5.63874L5.78243 1.17272C5.98895 0.95944 6.23412 0.790259 6.50395 0.674834C6.77378 0.559409 7.06298 0.5 7.35504 0.5C7.64711 0.5 7.93631 0.559409 8.20614 0.674834C8.47597 0.790259 8.72114 0.95944 8.92766 1.17272C9.13418 1.386 9.298 1.63919 9.40977 1.91785C9.52153 2.19652 9.57906 2.49518 9.57906 2.7968C9.57906 3.09842 9.52153 3.39709 9.40977 3.67575C9.298 3.95441 9.13418 4.20761 8.92766 4.42089L4.60317 8.88691C4.3946 9.10231 4.1117 9.22333 3.81673 9.22333C3.52175 9.22333 3.23886 9.10231 3.03028 8.88691C2.8217 8.6715 2.70452 8.37935 2.70452 8.07472C2.70452 7.77009 2.8217 7.47794 3.03028 7.26254L6.96168 3.20304" stroke="#3B3731" stroke-linecap="round" />
                </svg>
                Attach
                <input type="file" />
            </label>
            <div class="divider"></div>
            <label class="action dark-color-font">
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                    <path d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4.16662 5.3889C4.84163 5.3889 5.38884 4.84169 5.38884 4.16668C5.38884 3.49167 4.84163 2.94446 4.16662 2.94446C3.4916 2.94446 2.9444 3.49167 2.9444 4.16668C2.9444 4.84169 3.4916 5.3889 4.16662 5.3889Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M11.5001 7.83346L9.61421 5.94757C9.38501 5.71844 9.07419 5.58972 8.7501 5.58972C8.42601 5.58972 8.11519 5.71844 7.88599 5.94757L2.33344 11.5001" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Upload
                <input type="file" accept="image/*" />
            </label>
            <div class="counter fs-14-400-f-color">
                <span id="count">0</span>/3,000
            </div>
        </div>`;

  function getBadgeSvg(type) {
    return type === "groomer"
      ? `<svg xmlns="http://www.w3.org/2000/svg" width="21" height="23" viewBox="0 0 21 23" fill="none">
                <ellipse cx="10.9241" cy="11.3744" rx="6.44549" ry="6.06626" fill="white" />
                <path d="M10.6272 0.127384C10.4511 0.0439255 10.2623 0 10.0652 0C9.86812 0 9.6794 0.0439255 9.50326 0.127384L1.60626 3.63703C0.683615 4.04554 -0.00417476 4.99872 1.90757e-05 6.14957C0.0209883 10.507 1.73207 18.4795 8.95806 22.1033C9.65843 22.4547 10.472 22.4547 11.1724 22.1033C18.3984 18.4795 20.1095 10.507 20.1304 6.14957C20.1346 4.99872 19.4469 4.04554 18.5242 3.63703L10.6272 0.127384ZM6.07689 12.5715C6.2782 12.6242 6.49208 12.6505 6.71016 12.6505C8.19059 12.6505 9.39422 11.3899 9.39422 9.83931V7.02808H11.2479C11.7554 7.02808 12.2209 7.32677 12.4473 7.80556L12.7493 8.43369H15.4333C15.8024 8.43369 16.1044 8.74996 16.1044 9.1365V10.5421C16.1044 12.4836 14.603 14.0562 12.7493 14.0562H10.7362V16.2832C10.7362 16.6038 10.4888 16.8674 10.1785 16.8674C10.103 16.8674 10.0275 16.8498 9.96039 16.8191L5.82107 14.961C5.54428 14.838 5.36813 14.5525 5.36813 14.2406C5.36813 14.1177 5.3933 13.9991 5.44782 13.8892L6.07689 12.5715ZM6.03915 7.02808H8.05219V9.83931C8.05219 10.6168 7.45247 11.2449 6.71016 11.2449C5.96785 11.2449 5.36813 10.6168 5.36813 9.83931V7.73089C5.36813 7.34434 5.67009 7.02808 6.03915 7.02808ZM11.4073 9.1365C11.4073 8.9501 11.3366 8.77134 11.2107 8.63954C11.0849 8.50774 10.9142 8.43369 10.7362 8.43369C10.5583 8.43369 10.3876 8.50774 10.2618 8.63954C10.1359 8.77134 10.0652 8.9501 10.0652 9.1365C10.0652 9.3229 10.1359 9.50166 10.2618 9.63346C10.3876 9.76526 10.5583 9.83931 10.7362 9.83931C10.9142 9.83931 11.0849 9.76526 11.2107 9.63346C11.3366 9.50166 11.4073 9.3229 11.4073 9.1365Z" fill="#C9DDA0" />
            </svg>`
      : `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="22" viewBox="0 0 20 22" fill="none">
                <path d="M10.5114 0.120327C10.3372 0.0415119 10.1505 3.05176e-05 9.95555 3.05176e-05C9.76059 3.05176e-05 9.57393 0.0415119 9.3997 0.120327L1.58876 3.43469C0.676166 3.82047 -0.00412927 4.72061 1.88678e-05 5.80743C0.0207596 9.92238 1.7132 17.4513 8.86045 20.8735C9.55319 21.2053 10.3579 21.2053 11.0507 20.8735C18.1979 17.4513 19.8903 9.92238 19.9111 5.80743C19.9152 4.72061 19.2349 3.82047 18.3224 3.43469L10.5114 0.120327Z" fill="#CBDCE8" />
                <path d="M15.3611 5.76004L11.094 10.2939M9.19828 10.0725C7.87547 10.5803 6.81775 10.4934 5.76003 10.0741C6.02673 13.5108 7.62904 14.832 9.76527 15.3611C9.76527 15.3611 11.3745 14.2228 11.6065 11.5244C11.6316 11.2321 11.6439 11.0865 11.5836 10.9217C11.5228 10.7569 11.4033 10.639 11.1649 10.4027C10.7723 10.0144 10.5766 9.82022 10.3435 9.77115C10.1104 9.72314 9.80635 9.83942 9.19828 10.0725Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M6.5603 12.9327C6.5603 12.9327 7.89378 13.1909 9.22726 12.1614" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M8.69331 8.02663C8.69331 8.20346 8.62307 8.37305 8.49803 8.49809C8.37299 8.62313 8.2034 8.69337 8.02657 8.69337C7.84974 8.69337 7.68015 8.62313 7.55512 8.49809C7.43008 8.37305 7.35983 8.20346 7.35983 8.02663C7.35983 7.8498 7.43008 7.68022 7.55512 7.55518C7.68015 7.43014 7.84974 7.35989 8.02657 7.35989C8.2034 7.35989 8.37299 7.43014 8.49803 7.55518C8.62307 7.68022 8.69331 7.8498 8.69331 8.02663Z" fill="#CBDCE8" stroke="white" />
                <path d="M10.0268 6.2937V6.34704" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
            </svg>`;
  }

  function getConversationById(conversationId) {
    return conversations.find(
      (conversation) => conversation.id === conversationId,
    );
  }

  function getActiveTabId() {
    return (
      document.querySelector(".tablinks.active")?.dataset.tab ||
      "groomer-messages"
    );
  }

  function getProfileUrl(conversation) {
    if (conversation.profileUrl) {
      return conversation.profileUrl;
    }

    const base = config.baseUrl || "";
    return conversation.list.badge === "groomer"
      ? `${base}/profiles/groomer/groomer_profile`
      : `${base}/profiles/space/space_profile`;
  }

  function matchesActiveFilter(conversation) {
    if (showingArchived || !activeFilter || activeFilter === "most-recent") {
      return true;
    }

    if (activeFilter === "unread") {
      return conversation.list.unread === true;
    }

    if (activeFilter === "active-bookings") {
      return true;
    }

    return true;
  }

  function sortConversations(list) {
    return [...list].sort(
      (a, b) => (b.lastActivityAt || 0) - (a.lastActivityAt || 0),
    );
  }

  function getVisibleConversations(tabId) {
    const filtered = conversations.filter((conversation) => {
      if (conversation.deleted) {
        return false;
      }

      const matchesTab = conversation.tab === tabId;
      const matchesArchiveState = showingArchived
        ? conversation.archived === true
        : conversation.archived !== true;
      return (
        matchesTab && matchesArchiveState && matchesActiveFilter(conversation)
      );
    });

    return sortConversations(filtered);
  }

  function syncFilterButtons() {
    document.querySelectorAll(".filter[data-filter]").forEach((filterEl) => {
      const isActive = filterEl.dataset.filter === activeFilter;
      filterEl.classList.toggle("active", isActive);

      const existingCheck = filterEl.querySelector("svg");
      if (isActive && !existingCheck) {
        filterEl.insertAdjacentHTML("afterbegin", filterCheckSvg);
      } else if (!isActive && existingCheck) {
        existingCheck.remove();
      }
    });
  }

  function setActiveFilter(filterId) {
    // Clicking the active filter again clears it (back to full list).
    activeFilter = activeFilter === filterId ? null : filterId;
    syncFilterButtons();
    renderChatLists();
    refreshVisibleChat();
  }

  function bindFilters() {
    document.querySelectorAll(".filter[data-filter]").forEach((filterEl) => {
      filterEl.addEventListener("click", () => {
        if (showingArchived) {
          return;
        }
        setActiveFilter(filterEl.dataset.filter);
      });
    });
  }

  function renderChatListCard(conversation) {
    const { list } = conversation;
    const countMarkup = list.count
      ? `<p class="messages-count light-color-font">${list.count}</p>`
      : "";
    const marginClass = list.cardClass ? ` ${list.cardClass}` : "";

    return `
            ${list.dividerBefore ? '<div class="section-divider mt-2 mb-2"></div>' : ""}
            <div class="chat-card${list.unread ? " unread" : ""} cursor d-flex align-items-center gap-20${marginClass}" data-conversation-id="${conversation.id}">
                <div class="profile-pic">
                    <div class="profile-image-wrapper">
                        <img src="${list.image}" class="rounded-image" alt="">
                        <div class="top-left-svg">${getBadgeSvg(list.badge)}</div>
                    </div>
                </div>
                <div class="chat-info">
                    <div class="d-flex justify-content-between">
                        <div class="name">
                            <p class="dark-color-font">${list.name}</p>
                            <p class="simple-light-font">${list.subtitle}</p>
                        </div>
                        ${countMarkup}
                    </div>
                    <div class="d-flex align-items-center justify-content-between mt-2">
                        <p class="simple-font">${list.preview}</p>
                        <p class="time">${list.time}</p>
                    </div>
                </div>
            </div>
        `;
  }

  function renderEmptyListMessage() {
    return `
            <div class="chat-card d-flex align-items-center justify-content-center mt-4">
                <p class="simple-light-font">
                    ${showingArchived ? "No archived chats yet." : "No chats to show."}
                </p>
            </div>
        `;
  }

  function renderChatLists() {
    Object.entries(chatListsByTab).forEach(([tabId, listRoot]) => {
      if (!listRoot) return;

      const tabConversations = getVisibleConversations(tabId);
      listRoot.innerHTML = tabConversations.length
        ? tabConversations
            .map((conversation) => renderChatListCard(conversation))
            .join("")
        : renderEmptyListMessage();
    });

    bindConversationCards();
  }

  function refreshVisibleChat() {
    const visible = getVisibleConversations(getActiveTabId());
    if (visible.length) {
      const stillVisible = visible.some(
        (conversation) => conversation.id === activeConversationId,
      );
      renderChat(stillVisible ? activeConversationId : visible[0].id);
      return;
    }

    activeConversationId = null;
    messagesRoot.innerHTML = "";
    chatBox.innerHTML = "";
  }

  function updateSidebarMode() {
    if (!sidebar || !messagesTitle) return;

    sidebar.classList.toggle("is-archived-view", showingArchived);
    messagesTitle.textContent = showingArchived
      ? "Archived Messages"
      : "Messages";

    if (settingsArchived) {
      settingsArchived.hidden = showingArchived;
    }

    if (exitArchivedBtn) {
      exitArchivedBtn.hidden = !showingArchived;
    }
  }

  function openArchivedView() {
    showingArchived = true;
    document.querySelector(".archived")?.classList.remove("show");
    updateSidebarMode();
    renderChatLists();
    refreshVisibleChat();
  }

  function closeArchivedView() {
    showingArchived = false;
    updateSidebarMode();
    renderChatLists();
    refreshVisibleChat();
  }

  function archiveActiveChat() {
    const conversation = getConversationById(activeConversationId);
    const menu = document.querySelector(".archived-chat");
    menu?.classList.remove("show");

    if (!conversation || conversation.archived) {
      return;
    }

    conversation.archived = true;
    renderChatLists();
    refreshVisibleChat();
  }

  function unarchiveActiveChat() {
    const conversation = getConversationById(activeConversationId);
    const menu = document.querySelector(".archived-chat");
    menu?.classList.remove("show");

    if (!conversation || !conversation.archived) {
      return;
    }

    conversation.archived = false;
    renderChatLists();
    refreshVisibleChat();
  }

  function toggleArchiveActiveChat() {
    const conversation = getConversationById(activeConversationId);
    if (!conversation) return;

    if (conversation.archived) {
      unarchiveActiveChat();
      return;
    }

    archiveActiveChat();
  }

  function deleteActiveChat() {
    const conversation = getConversationById(activeConversationId);
    const menu = document.querySelector(".archived-chat");
    menu?.classList.remove("show");

    if (!conversation || conversation.deleted) {
      return;
    }

    conversation.deleted = true;
    conversation.archived = false;
    renderChatLists();
    refreshVisibleChat();
  }

  function renderMessages(chat) {
    messagesRoot.classList.remove("locked");
    messagesRoot.innerHTML = `${chat.messages
      .map(
        (message) => `
            <div class="message ${message.type}">
                <div class="bubble">
                    <p class="${message.type === "typing" ? "normal-light-color" : "normal-font-weight"}">
                        ${message.html}
                    </p>
                </div>
                <div class="time simple-light-font">${message.time}</div>
            </div>
        `,
      )
      .join("")}
        ${
          chat.quickReplies.length
            ? `
            <div class="quick-replies d-flex align-items-center justify-content-end gap-10">
                ${chat.quickReplies.map((reply) => `<button class="btn-custom btn-active-bg normal-font-bold">${reply}</button>`).join("")}
            </div>
        `
            : ""
        }`;

    messagesRoot.scrollTo({
      top: messagesRoot.scrollHeight,
      behavior: "smooth",
    });
  }

  function bindComposerInteractions() {
    const composerTextarea = document.getElementById("message");
    const composerCount = document.getElementById("count");
    const composerSendBtn = document.getElementById("sendBtn");
    const previewRow = document.getElementById("previewRow");

    if (composerTextarea && composerCount && composerSendBtn) {
      composerTextarea.addEventListener("input", () => {
        composerCount.textContent = composerTextarea.value.length;
        composerTextarea.style.height = "auto";
        composerTextarea.style.height = composerTextarea.scrollHeight + "px";
        composerSendBtn.classList.toggle(
          "active",
          composerTextarea.value.trim().length > 0,
        );
      });

      composerSendBtn.addEventListener("click", () => {
        if (!composerTextarea.value.trim()) return;

        alert("Message sent:\n\n" + composerTextarea.value);
        composerTextarea.value = "";
        composerCount.textContent = 0;
        composerSendBtn.classList.remove("active");
        composerTextarea.style.height = "auto";
      });
    }

    if (previewRow) {
      document
        .querySelectorAll('.actions-row input[type="file"]')
        .forEach((input) => {
          input.addEventListener("change", function () {
            Array.from(this.files).forEach((file) => {
              const reader = new FileReader();
              reader.onload = function (event) {
                const item = document.createElement("div");
                item.className = "preview-item";

                let preview;
                if (file.type.startsWith("image/")) {
                  preview = document.createElement("img");
                  preview.src = event.target.result;
                } else {
                  preview = document.createElement("div");
                  preview.style.cssText =
                    "width:60px;height:60px;border-radius:8px;background:#f0f0f0;display:flex;flex-direction:column;align-items:center;justify-content:center;font-size:9px;color:#555;padding:4px;text-align:center;word-break:break-all;box-sizing:border-box;";
                  preview.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6M4 4h16v16H4z"/></svg>${file.name.slice(0, 12)}`;
                }

                const btn = document.createElement("span");
                btn.className = "remove-btn";
                btn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                      <circle cx="8" cy="8" r="7.5" fill="white" stroke="#3B3731"/>
                      <path d="M5.68878 10.6667L10.6666 5.68896M5.68878 5.68896L10.6666 10.6667" stroke="#3B3731" stroke-linecap="round"/>
                    </svg>
                `;
                btn.addEventListener("click", () => item.remove());

                item.appendChild(preview);
                item.appendChild(btn);
                previewRow.appendChild(item);
              };
              reader.readAsDataURL(file);
            });

            this.value = "";
          });
        });
    }
  }

  function renderComposer() {
    chatBox.innerHTML = `
            <div class="preview-row" id="previewRow"></div>
            <div class="message-row">
                <div class="message-input">
                    <textarea id="message" placeholder="Write a message ..." maxlength="3000"></textarea>
                </div>
                <button class="send-btn" id="sendBtn">${sendSvg}</button>
            </div>
            ${attachmentActions}
        `;

    bindComposerInteractions();
  }

  function getHostName(conversation) {
    const { list, detail } = conversation;

    if (detail.hostName) {
      return detail.hostName;
    }

    // Space chats show "Hosted by Name" under Active now
    if (list.badge === "space") {
      return detail.subtitle;
    }

    return detail.displayName;
  }

  function getStudioName(conversation) {
    const { list, detail } = conversation;

    if (detail.studioName) {
      return detail.studioName;
    }

    return list.badge === "groomer" ? detail.subtitle : detail.displayName;
  }

  function getStatusLabel(detail) {
    if (detail.statusLabel) {
      return detail.statusLabel;
    }

    return detail.availability === "Available"
      ? "Active now"
      : detail.availability;
  }

  function isBookingCompleted(detail) {
    if (detail.bookingState === "completed") {
      return true;
    }

    if (detail.bookingState === "active") {
      return false;
    }

    return detail.bookingStatus === "Completed";
  }

  // Booking card icons live here.
  // Space = house icon, Groomer = brush icon
  function getBookingIconSvg(type) {
    if (type === "space") {
      return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="14" viewBox="0 0 16 14" fill="none">
            <path d="M13.9835 12.9244V4.08978C13.9835 4.06858 13.9853 4.04781 13.9885 4.02748L11.5999 1.99057C11.092 1.55804 10.741 1.25993 10.4433 1.06558C10.1558 0.877974 9.96266 0.817957 9.77786 0.817957C9.59321 0.817957 9.40123 0.878193 9.11408 1.06558C8.81631 1.25996 8.46444 1.5578 7.9558 1.99057L5.56551 4.02748C5.5688 4.04789 5.57221 4.0685 5.57221 4.08978V12.9244C5.57185 13.1499 5.37987 13.3333 5.1431 13.3333C4.90649 13.3332 4.71435 13.1498 4.71399 12.9244V4.75278L4.27147 5.1314C4.0948 5.28195 3.82264 5.26623 3.66467 5.09785C3.50733 4.92958 3.52206 4.67157 3.6982 4.52113L7.38254 1.3819H7.38421C7.87584 0.963595 8.27397 0.622591 8.62797 0.391405C8.99267 0.153336 9.35472 2.39994e-07 9.77786 0C10.2009 0 10.5629 0.153326 10.9277 0.391405C11.282 0.622647 11.6818 0.96344 12.1732 1.3819L15.8575 4.52113C16.0337 4.67157 16.0484 4.92958 15.891 5.09785C15.7331 5.26623 15.4609 5.28195 15.2843 5.1314L14.8417 4.75278V12.9244C14.8414 13.1498 14.6492 13.3332 14.4126 13.3333C14.1758 13.3333 13.9839 13.1499 13.9835 12.9244Z" fill="#3B3731"/>
            <path d="M1.94577 7.11203C1.94577 6.80354 1.85803 6.53885 1.73317 6.36015C1.60827 6.18157 1.46316 6.10651 1.33331 6.10651C1.20354 6.10662 1.05827 6.1817 0.933461 6.36015C0.808711 6.53885 0.720863 6.8037 0.720863 7.11203C0.720977 7.42046 0.808557 7.68532 0.933461 7.8639C1.05825 8.04226 1.20357 8.11593 1.33331 8.11604C1.46306 8.11604 1.60833 8.04218 1.73317 7.8639C1.85807 7.68532 1.94565 7.42046 1.94577 7.11203ZM2.66663 7.11203C2.66652 7.57155 2.53753 8.00267 2.31042 8.32741C2.08311 8.65242 1.74093 8.88905 1.33331 8.88905C0.925982 8.88894 0.584878 8.65215 0.357616 8.32741C0.130484 8.00266 0.000113694 7.57158 0 7.11203C0 6.65226 0.130384 6.22003 0.357616 5.89514C0.584878 5.5705 0.926063 5.33361 1.33331 5.3335C1.74087 5.3335 2.08311 5.5702 2.31042 5.89514C2.53765 6.22003 2.66663 6.65226 2.66663 7.11203Z" fill="#3B3731"/>
            <path d="M0.889038 12.9164V8.4164C0.889038 8.18629 1.08802 7.99976 1.33348 7.99976C1.57893 7.99976 1.77791 8.18629 1.77791 8.4164V12.9164C1.77773 13.1464 1.57882 13.3331 1.33348 13.3331C1.08814 13.3331 0.889225 13.1464 0.889038 12.9164Z" fill="#3B3731"/>
            <path d="M11.3683 9.93476C11.3683 9.57184 11.3667 9.34142 11.3436 9.17254C11.3222 9.01599 11.2894 8.97526 11.2696 8.95569C11.2498 8.93619 11.2086 8.90237 11.0492 8.88125C10.8775 8.85852 10.6421 8.85859 10.2729 8.85859H9.51631C9.14706 8.85859 8.91175 8.85852 8.74001 8.88125C8.58055 8.90237 8.53944 8.93619 8.51962 8.95569C8.49978 8.97526 8.46701 9.016 8.44561 9.17254C8.42252 9.34142 8.42094 9.57184 8.42094 9.93476V12.5046H11.3683V9.93476ZM10.6528 5.79191C10.885 5.79209 11.0735 5.9778 11.0739 6.2062C11.0739 6.43489 10.8852 6.62031 10.6528 6.62048H9.13639C8.904 6.62031 8.71534 6.43489 8.71534 6.2062C8.71569 5.9778 8.90422 5.79209 9.13639 5.79191H10.6528ZM10.6528 3.55542L10.7367 3.56351C10.9289 3.60188 11.0739 3.76926 11.0739 3.96971C11.0739 4.17015 10.9289 4.33753 10.7367 4.3759L10.6528 4.38399H9.13639C8.904 4.38382 8.71534 4.1984 8.71534 3.96971C8.71534 3.74101 8.904 3.55559 9.13639 3.55542H10.6528ZM12.2104 12.5046H15.5787C15.8113 12.5046 15.9998 12.6901 15.9998 12.9189C15.9994 13.1474 15.811 13.3332 15.5787 13.3332H0.421047C0.188728 13.3332 0.000354627 13.1474 0 12.9189C0 12.6901 0.188509 12.5046 0.421047 12.5046H7.57884V9.93476C7.57884 9.59543 7.57778 9.29932 7.61009 9.0625C7.64413 8.81351 7.72212 8.56883 7.92423 8.36987C8.12647 8.17088 8.37505 8.09428 8.62817 8.06077C8.86906 8.02891 9.17099 8.03002 9.51631 8.03002H10.2729C10.6182 8.03002 10.9201 8.02891 11.161 8.06077C11.4142 8.09428 11.6627 8.17088 11.865 8.36987C12.0671 8.56883 12.1451 8.81351 12.1791 9.0625C12.2114 9.29932 12.2104 9.59543 12.2104 9.93476V12.5046Z" fill="#3B3731"/>
            </svg>`;
    }

    return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
            <path d="M4.94585 11.5544C6.23108 12.8397 9.35693 11.798 11.9274 9.22713C14.4983 6.65667 15.5399 3.53082 14.2547 2.24559M8.72748 1.37259L9.30921 1.95473M6.69144 3.40904L7.27316 3.99077M4.94543 5.73636L5.52716 6.31809M4.36371 8.6454L4.94543 9.22713M11.9274 0.5L12.5091 1.08173M11.3457 3.99118L12.5091 5.15463M9.30962 6.02763L10.4731 7.19109M6.9823 7.77281L8.14575 8.93627" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4.94535 13.2998C5.42734 12.8178 5.42734 12.0364 4.94535 11.5544C4.46336 11.0724 3.6819 11.0724 3.19991 11.5544L0.872653 13.8816C0.390662 14.3636 0.390662 15.1451 0.872653 15.6271C1.35464 16.1091 2.13611 16.1091 2.6181 15.6271L4.94535 13.2998Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>`;
  }

  function getBookingTitle(detail) {
    const service = detail.bookingService || "Booking";

    if (isBookingCompleted(detail)) {
      return detail.petName ? `${service} · ${detail.petName}` : service;
    }

    return service;
  }

  function getBookingMeta(detail) {
    if (isBookingCompleted(detail)) {
      const parts = [
        detail.bookingType || "Past booking",
        detail.bookingDate,
        detail.bookingStatus || "Completed",
      ].filter(Boolean);

      return parts.join(" · ");
    }

    if (detail.bookingSchedule) {
      return detail.bookingSchedule;
    }

    return [detail.bookingDate, detail.bookingTime, detail.bookingLocation]
      .filter(Boolean)
      .join(" · ");
  }

  function renderHeader(conversation) {
    const { list, detail } = conversation;
    const tag = headerRoot.querySelector(".tag");
    const statusText = headerRoot.querySelector(".status-text");
    const statusDot = headerRoot.querySelector(".status-dot circle");
    const archiveMenuItem = headerRoot.querySelector(
      '.archived-chat[data-action="archive-chat"]',
    );
    const archiveMenuLabel = archiveMenuItem?.querySelector(".simple-font");
    const archiveMenuIcon = archiveMenuItem?.querySelector("svg path");
    const blockMenuLabel = headerRoot.querySelector(
      '.archived-chat [data-action="block-groomer"] .simple-font',
    );
    const profileLink = headerRoot.querySelector("[data-profile-link]");
    const bookingLink = headerRoot.querySelector("[data-booking-link]");
    const bookingIcon = headerRoot.querySelector("[data-booking-icon]");
    const bookingCompleted = isBookingCompleted(detail);

    // Space tab chats always use the space tag + house icon
    const isSpaceChat =
      conversation.tab === "space-messages" || list.badge === "space";
    const iconType = isSpaceChat ? "space" : detail.bookingIcon || "groomer";
    const tagType = isSpaceChat
      ? "space"
      : detail.tagClass || list.badge || "groomer";

    headerRoot.querySelector(".rounded-image.large-size").src = list.image;
    headerRoot.querySelector(".profile-image-wrapper .top-left-svg").innerHTML =
      getBadgeSvg(isSpaceChat ? "space" : list.badge);
    tag.textContent = detail.tag;
    tag.classList.toggle("groomer", tagType === "groomer");
    tag.classList.toggle("space", tagType === "space");
    tag.style.removeProperty("background");
    tag.style.removeProperty("color");

    const availabilityColor = detail.availabilityColor || "#C9DDA0";
    statusText.textContent = `${getStatusLabel(detail)} · ${getHostName(conversation)}`;
    // statusText.style.color = availabilityColor;
    // statusDot.setAttribute('fill', availabilityColor);

    headerRoot.querySelector(".studio-name").textContent =
      getStudioName(conversation);
    headerRoot.querySelector(".booking-title").textContent =
      getBookingTitle(detail);
    headerRoot.querySelector(".booking-meta").textContent =
      getBookingMeta(detail);
    headerRoot.querySelector(".booking-ref").textContent =
      detail.bookingReference;

    if (bookingLink) {
      bookingLink.classList.toggle(
        "chat-booking-card--completed",
        bookingCompleted,
      );
      bookingLink.classList.toggle(
        "chat-booking-card--active",
        !bookingCompleted,
      );
      bookingLink.href =
        detail.bookingUrl ||
        `${config.baseUrl || ""}my_bookings`;
    }

    if (bookingIcon) {
      bookingIcon.innerHTML = getBookingIconSvg(iconType);
    }

    const chevronPath = headerRoot.querySelector(".booking-chevron path");
    if (chevronPath) {
      chevronPath.setAttribute(
        "stroke",
        bookingCompleted ? "#D4D4D4" : "#C9DDA0",
      );
    }

    if (profileLink) {
      profileLink.href = getProfileUrl(conversation);
      profileLink.setAttribute(
        "aria-label",
        list.badge === "groomer"
          ? "View groomer profile"
          : "View space profile",
      );
    }

    if (archiveMenuLabel) {
      archiveMenuLabel.textContent = conversation.archived
        ? "Unarchive Chat"
        : "Archive Chat";
    }

    if (archiveMenuIcon) {
      archiveMenuIcon.setAttribute(
        "d",
        conversation.archived ? unarchiveIconPath : archiveIconPath,
      );
    }

    if (blockMenuLabel) {
      blockMenuLabel.textContent =
        list.badge === "space" ? "Block Space" : "Block Groomer";
    }
  }

  function setSelectedCard(selectedCard) {
    document
      .querySelectorAll(".chat-card.selected")
      .forEach((card) => card.classList.remove("selected"));
    selectedCard.classList.add("selected");
  }

  function playChatBodyAnimation() {
    if (!chatPanel) return;

    chatPanel.classList.remove("is-switching");
    // Force a reflow so the animation can restart on each chat click.
    void chatPanel.offsetWidth;
    chatPanel.classList.add("is-switching");
  }

  function renderChat(conversationId) {
    const conversation = getConversationById(conversationId);
    if (!conversation) return;

    const isSameChat = activeConversationId === conversationId;
    const screenDetail = {
      ...conversation.detail,
    };

    activeConversationId = conversationId;

    const selectedCard = document.querySelector(
      `[data-conversation-id="${conversationId}"]`,
    );
    if (selectedCard) {
      setSelectedCard(selectedCard);
    }

    renderHeader(conversation);
    renderMessages(screenDetail);
    renderComposer();

    if (!isSameChat) {
      playChatBodyAnimation();
    }
  }

  function bindConversationCards() {
    document
      .querySelectorAll(".chat-card[data-conversation-id]")
      .forEach((card) => {
        const conversationId = card.dataset.conversationId;

        card.classList.add("is-clickable");
        card.setAttribute("role", "button");
        card.setAttribute("tabindex", "0");
        card.addEventListener("click", () => renderChat(conversationId));
        card.addEventListener("keydown", (event) => {
          if (event.key === "Enter" || event.key === " ") {
            event.preventDefault();
            renderChat(conversationId);
          }
        });
      });
  }

  function bindMenus() {
    const settingsBtn = document.querySelector(".setting");
    const archived = document.querySelector(".archived");
    const dotsBtn = document.querySelector(".dots-svg");
    const archivedChat = document.querySelector(".archived-chat");
    const openArchivedTrigger = document.querySelector(
      '[data-action="open-archived"]',
    );
    const archiveChatTrigger = document.querySelector(
      '[data-action="archive-chat"]',
    );

    if (settingsBtn && archived) {
      settingsBtn.addEventListener("click", (event) => {
        event.stopPropagation();
        archived.classList.toggle("show");
      });
    }

    if (dotsBtn && archivedChat) {
      dotsBtn.addEventListener("click", (event) => {
        if (event.target.closest(".chat-menu-item")) return;
        event.stopPropagation();
        archivedChat.classList.toggle("show");
      });
    }

    openArchivedTrigger?.addEventListener("click", (event) => {
      event.stopPropagation();
      openArchivedView();
    });

    archiveChatTrigger?.addEventListener("click", (event) => {
      event.stopPropagation();
      toggleArchiveActiveChat();
    });

    archivedChat
      ?.querySelector('[data-action="block-groomer"]')
      ?.addEventListener("click", (event) => {
        event.stopPropagation();
        archivedChat.classList.remove("show");
      });

    archivedChat
      ?.querySelector('[data-action="delete-chat"]')
      ?.addEventListener("click", (event) => {
        event.stopPropagation();
        deleteActiveChat();
      });

    exitArchivedBtn?.addEventListener("click", closeArchivedView);

    document.addEventListener("click", () => {
      archived?.classList.remove("show");
      archivedChat?.classList.remove("show");
    });
  }

  function activateMessagesTab(tabName) {
    const theme = {
      "groomer-messages": {
        active: "#FFC97A",
        bg: "rgba(255, 201, 122, 0.13)",
      },
      "space-messages": {
        active: "#FFA899",
        bg: "rgba(255, 168, 153, 0.13)",
      },
    };

    const selected = theme[tabName];
    if (!selected) return;

    document.querySelectorAll(".tab-wrapper .tablinks").forEach((tab) => {
      tab.classList.toggle("active", tab.dataset.tab === tabName);
    });

    document.querySelectorAll(".tab-wrapper .tabcontent").forEach((panel) => {
      const isActive = panel.dataset.tabContent === tabName;
      panel.style.display = isActive ? panel.dataset.display || "flex" : "none";
    });

    document.documentElement.style.setProperty("--active-bg", selected.active);
    document.documentElement.style.setProperty(
      "--active-bg-light",
      selected.bg,
    );

    const firstConversation = getVisibleConversations(tabName)[0];
    if (firstConversation) {
      renderChat(firstConversation.id);
    } else {
      refreshVisibleChat();
    }
  }

  function bindTabThemeColors() {
    document.querySelectorAll(".tab-wrapper .tablinks").forEach((tab) => {
      tab.addEventListener("click", function (event) {
        event.preventDefault();
        activateMessagesTab(this.dataset.tab);
      });
    });
  }

  function keepWheelInside(scroller) {
    if (!scroller) return;

    scroller.addEventListener(
      "wheel",
      (event) => {
        if (event.ctrlKey || event.metaKey) return;

        const maxScroll = scroller.scrollHeight - scroller.clientHeight;
        if (maxScroll <= 1) return;

        let delta = event.deltaY;
        if (event.deltaMode === 1) {
          delta *= 16;
        } else if (event.deltaMode === 2) {
          delta *= scroller.clientHeight;
        }

        event.preventDefault();
        scroller.scrollTop = Math.min(
          maxScroll,
          Math.max(0, scroller.scrollTop + delta),
        );
      },
      { passive: false },
    );
  }

  function init() {
    updateSidebarMode();
    syncFilterButtons();
    renderChatLists();
    bindComposerInteractions();
    bindMenus();
    bindFilters();
    bindTabThemeColors();
    keepWheelInside(messagesRoot);

    const initialConversation = getVisibleConversations("groomer-messages")[0];
    if (initialConversation) {
      renderChat(initialConversation.id);
    }
  }

  init();
})();
