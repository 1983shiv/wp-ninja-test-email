import React, { useState, useEffect } from 'react';

const LogsViewer = ({ restUrl, nonce }) => {
    const [logs, setLogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState('');
    const [debouncedSearch, setDebouncedSearch] = useState('');
    const [orderBy, setOrderBy] = useState('time');
    const [order, setOrder] = useState('DESC');
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(1);
    const [perPage] = useState(10);
    const [selectedLog, setSelectedLog] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [message, setMessage] = useState('');

    // Debounce search input
    useEffect(() => {
        const timer = setTimeout(() => {
            setDebouncedSearch(search);
            setCurrentPage(1);
        }, 500);

        return () => clearTimeout(timer);
    }, [search]);

    useEffect(() => {
        fetchLogs();
    }, [debouncedSearch, orderBy, order, currentPage]);

    const fetchLogs = async () => {
        setLoading(true);
        try {
            const params = new URLSearchParams({
                search: debouncedSearch,
                orderby: orderBy,
                order,
                page: currentPage,
                per_page: perPage
            });

            const response = await fetch(`${restUrl}/logs?${params}`, {
                headers: { 'X-WP-Nonce': nonce }
            });
            const data = await response.json();

            if (data.success) {
                setLogs(data.logs || []);
                setTotalPages(data.total_pages || 1);
            }
        } catch (error) {
            console.error('Error fetching logs:', error);
            setMessage('Error loading logs');
        } finally {
            setLoading(false);
        }
    };

    const handleSort = (column) => {
        if (orderBy === column) {
            setOrder(order === 'ASC' ? 'DESC' : 'ASC');
        } else {
            setOrderBy(column);
            setOrder('DESC');
        }
        setCurrentPage(1);
    };

    const handleSearch = (e) => {
        setSearch(e.target.value);
    };

    const handleDelete = async (logId) => {
        if (!confirm('Are you sure you want to delete this log?')) {
            return;
        }

        try {
            const response = await fetch(`${restUrl}/logs/${logId}`, {
                method: 'DELETE',
                headers: { 'X-WP-Nonce': nonce }
            });
            const data = await response.json();

            if (data.success) {
                setMessage('Log deleted successfully');
                fetchLogs();
                setTimeout(() => setMessage(''), 3000);
            }
        } catch (error) {
            console.error('Error deleting log:', error);
            setMessage('Error deleting log');
        }
    };

    const viewLog = (log) => {
        setSelectedLog(log);
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setSelectedLog(null);
    };

    const SortIcon = ({ column }) => {
        if (orderBy !== column) return <span className="nin-text-gray-400">↕</span>;
        return <span className="nin-text-blue-600">{order === 'ASC' ? '↑' : '↓'}</span>;
    };

    if (loading) {
        return (
            <div className="nin-flex nin-justify-center nin-items-center nin-p-8">
                <div className="nin-text-lg">Loading logs...</div>
            </div>
        );
    }

    return (
        <div className="nin-max-w-7xl nin-mx-auto nin-p-6">
            <div className="nin-mb-8">
                <h2 className="nin-text-2xl nin-font-bold nin-text-gray-800">Email Logs</h2>
                <p className="nin-text-gray-600 nin-mt-2">
                    View all logged outgoing emails from your WordPress site
                </p>
            </div>

            {message && (
                <div className="nin-mb-4 nin-p-4 nin-bg-green-100 nin-border nin-border-green-400 nin-text-green-700 nin-rounded">
                    {message}
                </div>
            )}

            {/* Search Bar */}
            <div className="nin-mb-4">
                <input
                    type="text"
                    value={search}
                    onChange={handleSearch}
                    placeholder="Search by recipient, subject, or body..."
                    className="nin-w-full nin-px-4 nin-py-2 nin-border nin-border-gray-300 nin-rounded-lg focus:nin-outline-none focus:nin-ring-2 focus:nin-ring-blue-500"
                />
            </div>

            {/* Logs Table */}
            <div className="nin-bg-white nin-shadow nin-rounded-lg nin-overflow-hidden">
                {logs.length === 0 ? (
                    <div className="nin-p-8 nin-text-center nin-text-gray-500">
                        <p className="nin-text-lg">No email logs found</p>
                        <p className="nin-text-sm nin-mt-2">Send a test email to see logs appear here</p>
                    </div>
                ) : (
                    <div className="nin-overflow-x-auto">
                        <table className="nin-min-w-full nin-divide-y nin-divide-gray-200">
                            <thead className="nin-bg-gray-50">
                                <tr>
                                    <th
                                        onClick={() => handleSort('time')}
                                        className="nin-px-6 nin-py-3 nin-text-left nin-text-xs nin-font-medium nin-text-gray-500 nin-uppercase nin-tracking-wider nin-cursor-pointer hover:nin-bg-gray-100"
                                    >
                                        Time <SortIcon column="time" />
                                    </th>
                                    <th
                                        onClick={() => handleSort('to_email')}
                                        className="nin-px-6 nin-py-3 nin-text-left nin-text-xs nin-font-medium nin-text-gray-500 nin-uppercase nin-tracking-wider nin-cursor-pointer hover:nin-bg-gray-100"
                                    >
                                        To <SortIcon column="to_email" />
                                    </th>
                                    <th
                                        onClick={() => handleSort('subject')}
                                        className="nin-px-6 nin-py-3 nin-text-left nin-text-xs nin-font-medium nin-text-gray-500 nin-uppercase nin-tracking-wider nin-cursor-pointer hover:nin-bg-gray-100"
                                    >
                                        Subject <SortIcon column="subject" />
                                    </th>
                                    <th
                                        onClick={() => handleSort('status')}
                                        className="nin-px-6 nin-py-3 nin-text-left nin-text-xs nin-font-medium nin-text-gray-500 nin-uppercase nin-tracking-wider nin-cursor-pointer hover:nin-bg-gray-100"
                                    >
                                        Status <SortIcon column="status" />
                                    </th>
                                    <th className="nin-px-6 nin-py-3 nin-text-right nin-text-xs nin-font-medium nin-text-gray-500 nin-uppercase nin-tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="nin-bg-white nin-divide-y nin-divide-gray-200">
                                {logs.map((log) => (
                                    <tr key={log.id} className="hover:nin-bg-gray-50">
                                        <td className="nin-px-6 nin-py-4 nin-whitespace-nowrap nin-text-sm nin-text-gray-900">
                                            {log.time}
                                        </td>
                                        <td className="nin-px-6 nin-py-4 nin-whitespace-nowrap nin-text-sm nin-text-gray-900">
                                            {log.to_email}
                                        </td>
                                        <td className="nin-px-6 nin-py-4 nin-text-sm nin-text-gray-900">
                                            {log.subject.length > 50 ? log.subject.substring(0, 50) + '...' : log.subject}
                                        </td>
                                        <td className="nin-px-6 nin-py-4 nin-whitespace-nowrap">
                                            <span className="nin-px-2 nin-inline-flex nin-text-xs nin-leading-5 nin-font-semibold nin-rounded-full nin-bg-green-100 nin-text-green-800">
                                                {log.status}
                                            </span>
                                        </td>
                                        <td className="nin-px-6 nin-py-4 nin-whitespace-nowrap nin-text-right nin-text-sm nin-font-medium">
                                            <button
                                                onClick={() => viewLog(log)}
                                                className="nin-text-blue-600 hover:nin-text-blue-900 nin-mr-4"
                                            >
                                                View
                                            </button>
                                            <button
                                                onClick={() => handleDelete(log.id)}
                                                className="nin-text-red-600 hover:nin-text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* Pagination */}
            {totalPages > 1 && (
                <div className="nin-mt-4 nin-flex nin-justify-between nin-items-center">
                    <div className="nin-text-sm nin-text-gray-700">
                        Page {currentPage} of {totalPages}
                    </div>
                    <div className="nin-flex nin-gap-2">
                        <button
                            onClick={() => setCurrentPage(prev => Math.max(1, prev - 1))}
                            disabled={currentPage === 1}
                            className="nin-px-4 nin-py-2 nin-border nin-border-gray-300 nin-rounded nin-bg-white hover:nin-bg-gray-50 disabled:nin-opacity-50 disabled:nin-cursor-not-allowed"
                        >
                            Previous
                        </button>
                        <button
                            onClick={() => setCurrentPage(prev => Math.min(totalPages, prev + 1))}
                            disabled={currentPage === totalPages}
                            className="nin-px-4 nin-py-2 nin-border nin-border-gray-300 nin-rounded nin-bg-white hover:nin-bg-gray-50 disabled:nin-opacity-50 disabled:nin-cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>
                </div>
            )}

            {/* Modal */}
            {showModal && selectedLog && (
                <div className="nin-fixed nin-inset-0 nin-bg-black nin-bg-opacity-50 nin-flex nin-items-center nin-justify-center nin-z-50" onClick={closeModal}>
                    <div className="nin-bg-white nin-rounded-lg nin-p-6 nin-max-w-3xl nin-w-full nin-mx-4 nin-max-h-[90vh] nin-overflow-y-auto" onClick={(e) => e.stopPropagation()}>
                        <div className="nin-flex nin-justify-between nin-items-start nin-mb-4">
                            <h3 className="nin-text-xl nin-font-bold nin-text-gray-800">Email Details</h3>
                            <button
                                onClick={closeModal}
                                className="nin-text-gray-400 hover:nin-text-gray-600 nin-text-2xl nin-leading-none"
                            >
                                ×
                            </button>
                        </div>
                        
                        <div className="nin-space-y-4">
                            <div>
                                <label className="nin-block nin-text-sm nin-font-medium nin-text-gray-700 nin-mb-1">Time</label>
                                <div className="nin-text-gray-900">{selectedLog.time}</div>
                            </div>
                            
                            <div>
                                <label className="nin-block nin-text-sm nin-font-medium nin-text-gray-700 nin-mb-1">To</label>
                                <div className="nin-text-gray-900">{selectedLog.to_email}</div>
                            </div>
                            
                            <div>
                                <label className="nin-block nin-text-sm nin-font-medium nin-text-gray-700 nin-mb-1">Subject</label>
                                <div className="nin-text-gray-900">{selectedLog.subject}</div>
                            </div>
                            
                            <div>
                                <label className="nin-block nin-text-sm nin-font-medium nin-text-gray-700 nin-mb-1">Status</label>
                                <span className="nin-px-2 nin-inline-flex nin-text-xs nin-leading-5 nin-font-semibold nin-rounded-full nin-bg-green-100 nin-text-green-800">
                                    {selectedLog.status}
                                </span>
                            </div>
                            
                            <div>
                                <label className="nin-block nin-text-sm nin-font-medium nin-text-gray-700 nin-mb-1">Email Body</label>
                                <div className="nin-bg-gray-50 nin-p-4 nin-rounded nin-border nin-border-gray-200 nin-max-h-96 nin-overflow-y-auto nin-text-sm nin-text-gray-900">
                                    {selectedLog.body.includes('<') && selectedLog.body.includes('>') ? (
                                        <div dangerouslySetInnerHTML={{ __html: selectedLog.body }} />
                                    ) : (
                                        <div className="nin-whitespace-pre-wrap">{selectedLog.body}</div>
                                    )}
                                </div>
                            </div>
                        </div>
                        
                        <div className="nin-mt-6 nin-flex nin-justify-end">
                            <button
                                onClick={closeModal}
                                className="nin-px-4 nin-py-2 nin-bg-blue-600 nin-text-white nin-rounded hover:nin-bg-blue-700"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

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
            {currentPage === 'ninja-email-test-logs' ? (
                <LogsViewer restUrl={restUrl} nonce={nonce} />
            ) : (
                <>
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
                </>
            )}
        </div>
    );
};

export default AdminApp;
