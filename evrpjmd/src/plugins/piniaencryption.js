import CryptoJS from 'crypto-js';

const secretKey = '8xb82,rFfurZ';

export const encryptData = (data) => {
  return CryptoJS.AES.encrypt(JSON.stringify(data), secretKey).toString();
};

export const decryptData = (encryptedData) => {
  const bytes = CryptoJS.AES.decrypt(encryptedData, secretKey);
  return JSON.parse(bytes.toString(CryptoJS.enc.Utf8));
};

export const piniaEncryptionPlugin = (context) => {
  const store = context.store;
  
  // Encrypt the state before saving to localStorage or other persistence methods
  store.$subscribe((mutation, state) => {
    const encryptedState = encryptData(state);
    localStorage.setItem(`evarpjmd-${store.$id}`, encryptedState); // Store encrypted data
  });

  // Decrypt state when the store is initialized
  const encryptedState = localStorage.getItem(`evarpjmd-${store.$id}`);
  if (encryptedState) {
    store.$patch(decryptData(encryptedState)); // Decrypt and apply the state
  }
};