/**
 * Agora RTM Chat Logic
 * Handles real-time messaging using Agora RTM SDK
 */

$(document).ready(function() {
    let rtmClient = null;
    let rtmChannel = null;
    let currentUserAccount = null;
    let appId = null;

    // Fetch Token and Initialize
    $.getJSON('api/agora_token.php', function(data) {
        if (data.success) {
            appId = data.appId;
            currentUserAccount = data.userAccount;
            initializeRTM(data.token);
        } else {
            console.error('Failed to get Agora token:', data.error);
        }
    });

    async function initializeRTM(token) {
        rtmClient = AgoraRTM.createInstance(appId);

        // Event listener for connection state changes
        rtmClient.on('ConnectionStateChanged', (newState, reason) => {
            console.log('Connection state changed to ' + newState + ' reason: ' + reason);
        });

        // Event listener for receiving peer messages (One-to-One)
        rtmClient.on('MessageFromPeer', ({ text }, peerId) => {
            console.log('Message from peer ' + peerId + ': ' + text);
            // This is handled by the receiver logic
            if (typeof window.onIncomingRtmMessage === 'function') {
                window.onIncomingRtmMessage(text, peerId);
            }
        });

        try {
            await rtmClient.login({ token: token, uid: currentUserAccount });
            console.log('Agora RTM login success as ' + currentUserAccount);
        } catch (err) {
            console.error('Agora RTM login failure', err);
        }
    }

    // Global function to send message via RTM
    window.sendRtmMessage = async function(peerId, text) {
        if (!rtmClient) {
            console.warn('RTM Client not initialized');
            return false;
        }
        try {
            await rtmClient.sendMessageToPeer({ text: text }, peerId);
            console.log('RTM message sent to ' + peerId);
            return true;
        } catch (err) {
            console.error('RTM message send failed', err);
            return false;
        }
    };
});
