import React from 'react';
import { createRoot } from 'react-dom/client';
import AdminApp from './AdminApp';
import './styles.css';

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('ninja-email-test-admin-root');
    
    if (container) {
        const root = createRoot(container);
        root.render(<AdminApp />);
    }
});
