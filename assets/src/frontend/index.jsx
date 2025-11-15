import React from 'react';
import { createRoot } from 'react-dom/client';
import FrontendApp from './FrontendApp';
import './styles.css';

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('ninja-email-test-frontend-root');
    
    if (container) {
        const root = createRoot(container);
        root.render(<FrontendApp />);
    }
});
