import React, { useState, useEffect } from 'react';

const AdminApp = () => {
    const [settings, setSettings] = useState({});
    const [loading, setLoading] = useState(true);
    const [message, setMessage] = useState('');
    const [logStats, setLogStats] = useState({
        total: 0,
        today: 0,
        week: 0,
        month: 0
    });
    const [testEmail, setTestEmail] = useState({
        to: '',
        subject: '',
        message: '',
        format: 'text'
    });
    const [sendingEmail, setSendingEmail] = useState(false);
    
    const { restUrl, nonce, currentPage } = window.ninjaemailtestAdmin || {};
    
    useEffect(() => {
        fetchSettings();
        fetchLogStats();
        // Set default recipient to current user's email if available
        if (window.ninjaemailtestAdmin && window.ninjaemailtestAdmin.userEmail) {
            setTestEmail(prev => ({ ...prev, to: window.ninjaemailtestAdmin.userEmail }));
        }
    }, []);
    
    const fetchLogStats = async () => {
        try {
            const response = await fetch(`${restUrl}/logs/stats`, {
                headers: { 'X-WP-Nonce': nonce }
            });
            const data = await response.json();
            if (data.success && data.stats) {
                setLogStats(data.stats);
            }
        } catch (error) {
            console.error('Error fetching log stats:', error);
        }
    };
    
    const fetchSettings = async () => {
        try {
            const response = await fetch(`${restUrl}/admin/settings`, {
                headers: { 'X-WP-Nonce': nonce }
            });
            const data = await response.json();
            if (data.success) {
                setSettings(data.settings);
            }
        } catch (error) {
            console.error('Error fetching settings:', error);
        } finally {
            setLoading(false);
        }
    };
    
    const saveSettings = async () => {
        setLoading(true);
        try {
            const response = await fetch(`${restUrl}/admin/settings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify(settings)
            });
            const data = await response.json();
            if (data.success) {
                setMessage('Settings saved successfully!');
                setTimeout(() => setMessage(''), 3000);
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            setMessage('Error saving settings');
        } finally {
            setLoading(false);
        }
    };
    
    const saveSettingsWithData = async (newSettings) => {
        try {
            const response = await fetch(`${restUrl}/admin/settings`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify(newSettings)
            });
            const data = await response.json();
            if (data.success) {
                setMessage('Settings updated!');
                setTimeout(() => setMessage(''), 2000);
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            setMessage('Error saving settings');
        }
    };

    const handleSendTestEmail = async (e) => {
        e.preventDefault();
        setSendingEmail(true);
        setMessage('');
        
        try {
            const response = await fetch(`${restUrl}/test-email`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify(testEmail)
            });
            
            const data = await response.json();
            
            if (data.success) {
                setMessage(data.message || 'Test email sent successfully!');
                // Reset form except recipient
                setTestEmail(prev => ({
                    ...prev,
                    subject: '',
                    message: '',
                    format: 'text'
                }));
                // Refresh log stats after sending email
                fetchLogStats();
            } else {
                setMessage(data.message || 'Failed to send test email');
            }
        } catch (error) {
            console.error('Error sending test email:', error);
            setMessage('Error sending test email. Please try again.');
        } finally {
            setSendingEmail(false);
            setTimeout(() => setMessage(''), 5000);
        }
    };
    
    if (loading) {
        return <div className="nin-p-4">Loading...</div>;
    }
    
    return (
        <div className="nin-max-w-4xl nin-mx-auto nin-p-6">
            <div className="nin-mb-8">
                <h2 className="nin-text-2xl nin-font-bold nin-text-gray-800">
                    {currentPage === 'ninja-email-test-settings' ? 'Ninja Email Test Settings' : 'Ninja Email Test Dashboard'}
                </h2>
                <p className="nin-text-gray-600 nin-mt-2">
                    {currentPage === 'ninja-email-test-settings' 
                        ? 'Configure your plugin settings and preferences'
                        : 'Manage your plugin settings and configuration'
                    }
                </p>
            </div>
            
            {message && (
                <div className="nin-mb-4 nin-p-4 nin-bg-green-100 nin-border nin-border-green-400 nin-text-green-700 nin-rounded">
                    {message}
                </div>
            )}
            
            {currentPage === 'ninja-email-test' && (
                <>
                    <div className="nin-bg-white nin-shadow nin-rounded-lg nin-p-6 nin-mb-6">
                        <h3 className="nin-text-xl nin-font-semibold nin-mb-4">Send Test Email</h3>
                        <form onSubmit={handleSendTestEmail} className="nin-space-y-4">
                            <div>
                                <label className="nin-block nin-mb-2 nin-font-medium nin-text-gray-700">
                                    Recipient Email <span className="nin-text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    value={testEmail.to}
                                    onChange={(e) => setTestEmail({...testEmail, to: e.target.value})}
                                    required
                                    placeholder="email@example.com"
                                    className="nin-w-full nin-px-3 nin-py-2 nin-border nin-border-gray-300 nin-rounded focus:nin-outline-none focus:nin-ring-2 focus:nin-ring-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label className="nin-block nin-mb-2 nin-font-medium nin-text-gray-700">
                                    Subject (optional)
                                </label>
                                <input
                                    type="text"
                                    value={testEmail.subject}
                                    onChange={(e) => setTestEmail({...testEmail, subject: e.target.value})}
                                    placeholder="Leave empty for default subject"
                                    className="nin-w-full nin-px-3 nin-py-2 nin-border nin-border-gray-300 nin-rounded focus:nin-outline-none focus:nin-ring-2 focus:nin-ring-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label className="nin-block nin-mb-2 nin-font-medium nin-text-gray-700">
                                    Message (optional)
                                </label>
                                <textarea
                                    value={testEmail.message}
                                    onChange={(e) => setTestEmail({...testEmail, message: e.target.value})}
                                    placeholder="Leave empty for default message"
                                    rows="5"
                                    className="nin-w-full nin-px-3 nin-py-2 nin-border nin-border-gray-300 nin-rounded focus:nin-outline-none focus:nin-ring-2 focus:nin-ring-blue-500"
                                />
                            </div>
                            
                            <div>
                                <label className="nin-block nin-mb-2 nin-font-medium nin-text-gray-700">
                                    Email Format
                                </label>
                                <div className="nin-flex nin-space-x-4">
                                    <label className="nin-flex nin-items-center">
                                        <input
                                            type="radio"
                                            value="text"
                                            checked={testEmail.format === 'text'}
                                            onChange={(e) => setTestEmail({...testEmail, format: e.target.value})}
                                            className="nin-mr-2"
                                        />
                                        <span>Plain Text</span>
                                    </label>
                                    <label className="nin-flex nin-items-center">
                                        <input
                                            type="radio"
                                            value="html"
                                            checked={testEmail.format === 'html'}
                                            onChange={(e) => setTestEmail({...testEmail, format: e.target.value})}
                                            className="nin-mr-2"
                                        />
                                        <span>HTML</span>
                                    </label>
                                </div>
                            </div>
                            
                            <button
                                type="submit"
                                disabled={sendingEmail}
                                className="nin-px-6 nin-py-3 nin-bg-blue-600 nin-text-white nin-rounded-lg hover:nin-bg-blue-700 disabled:nin-opacity-50 disabled:nin-cursor-not-allowed nin-transition-colors"
                            >
                                {sendingEmail ? 'Sending...' : 'Send Test Email'}
                            </button>
                        </form>
                    </div>
                    
                    <div className="nin-bg-white nin-shadow nin-rounded-lg nin-p-6">
                        <h3 className="nin-text-xl nin-font-semibold nin-mb-4">Dashboard Overview</h3>
                        <div className="nin-grid nin-grid-cols-1 md:nin-grid-cols-3 nin-gap-4">
                            <div className="nin-p-4 nin-bg-blue-50 nin-rounded-lg">
                                <div className="nin-text-3xl nin-font-bold nin-text-blue-600">{logStats.total || 0}</div>
                                <div className="nin-text-gray-600">Total Emails Logged</div>
                            </div>
                            <div className="nin-p-4 nin-bg-green-50 nin-rounded-lg">
                                <div className="nin-text-3xl nin-font-bold nin-text-green-600">{logStats.today || 0}</div>
                                <div className="nin-text-gray-600">Sent Today</div>
                            </div>
                            <div className="nin-p-4 nin-bg-purple-50 nin-rounded-lg">
                                <div className="nin-text-3xl nin-font-bold nin-text-purple-600">{logStats.week || 0}</div>
                                <div className="nin-text-gray-600">Sent This Week</div>
                            </div>
                        </div>
                        <div className="nin-mt-4 nin-text-sm nin-text-gray-500">
                            <p>📊 Logs are automatically cleaned up after 30 days</p>
                            <p>📧 All outgoing emails from WordPress are captured</p>
                        </div>
                    </div>
                </>
            )}
            
            {currentPage === 'ninja-email-test-settings' && (
                <div className="nin-bg-white nin-shadow nin-rounded-lg nin-p-6">
                    <h3 className="nin-text-xl nin-font-semibold nin-mb-4">Settings</h3>
                    <div className="nin-space-y-4">
                        <div>
                            <label className="nin-block nin-mb-2 nin-font-medium">
                                Admin Capability
                            </label>
                            <input
                                type="text"
                                value={settings.admin_capability || 'manage_options'}
                                onChange={(e) => setSettings({...settings, admin_capability: e.target.value})}
                                className="nin-w-full nin-px-3 nin-py-2 nin-border nin-rounded"
                            />
                        </div>
                        
                        <button
                            onClick={saveSettings}
                            disabled={loading}
                            className="nin-px-4 nin-py-2 nin-bg-blue-600 nin-text-white nin-rounded hover:nin-bg-blue-700 disabled:nin-opacity-50"
                        >
                            {loading ? 'Saving...' : 'Save Settings'}
                        </button>
                    </div>
                    
                    <div className="nin-mt-8 nin-border-t nin-pt-6">
                        <h4 className="nin-text-lg nin-font-semibold nin-mb-4">Plugin Options Inspector</h4>
                        <div className="nin-bg-gray-50 nin-p-4 nin-rounded nin-border">
                            <div className="nin-mb-4">
                                <label className="nin-flex nin-items-center nin-space-x-2">
                                    <input
                                        type="checkbox"
                                        checked={settings.enabled || false}
                                        onChange={(e) => {
                                            const newSettings = {...settings, enabled: e.target.checked};
                                            setSettings(newSettings);
                                            // Auto-save when toggled
                                            saveSettingsWithData(newSettings);
                                        }}
                                        className="nin-rounded"
                                    />
                                    <span className="nin-font-medium">Enable Plugin</span>
                                </label>
                            </div>
                            <p className="nin-text-sm nin-text-gray-600 nin-mb-2">Current plugin options stored in database:</p>
                            <pre className="nin-text-xs nin-bg-white nin-p-3 nin-rounded nin-border nin-overflow-x-auto">{JSON.stringify(settings, null, 2)}</pre>
                            <p className="nin-text-xs nin-text-gray-500 nin-mt-2">Option key: <code>ninja_test_email_options</code></p>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default AdminApp;
