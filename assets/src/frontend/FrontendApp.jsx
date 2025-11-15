import React, { useState } from 'react';

const FrontendApp = () => {
    const [formData, setFormData] = useState({
        name: '',
        email: '',
        message: ''
    });
    const [submitting, setSubmitting] = useState(false);
    const [response, setResponse] = useState('');
    
    const { restUrl, nonce } = window.ninjaemailtestFrontend || {};
    
    const handleSubmit = async (e) => {
        e.preventDefault();
        setSubmitting(true);
        setResponse('');
        
        try {
            const res = await fetch(`${restUrl}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': nonce
                },
                body: JSON.stringify(formData)
            });
            
            const data = await res.json();
            
            if (data.success) {
                setResponse('Form submitted successfully!');
                setFormData({ name: '', email: '', message: '' });
            } else {
                setResponse('Error submitting form');
            }
        } catch (error) {
            console.error('Error:', error);
            setResponse('Error submitting form');
        } finally {
            setSubmitting(false);
        }
    };
    
    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };
    
    return (
        <div className="nin-max-w-2xl nin-mx-auto nin-p-6">
            <div className="nin-bg-white nin-shadow-lg nin-rounded-lg nin-p-8">
                <h2 className="nin-text-2xl nin-font-bold nin-mb-6 nin-text-gray-800">
                    Contact Form
                </h2>
                
                {response && (
                    <div className={`nin-mb-4 nin-p-4 nin-rounded ${
                        response.includes('success') 
                            ? 'nin-bg-green-100 nin-text-green-700'
                            : 'nin-bg-red-100 nin-text-red-700'
                    }`}>
                        {response}
                    </div>
                )}
                
                <form onSubmit={handleSubmit} className="nin-space-y-4">
                    <div>
                        <label className="nin-block nin-mb-2 nin-font-medium">Name</label>
                        <input
                            type="text"
                            name="name"
                            value={formData.name}
                            onChange={handleChange}
                            required
                            className="nin-w-full nin-px-4 nin-py-2 nin-border nin-rounded-lg"
                        />
                    </div>
                    
                    <div>
                        <label className="nin-block nin-mb-2 nin-font-medium">Email</label>
                        <input
                            type="email"
                            name="email"
                            value={formData.email}
                            onChange={handleChange}
                            required
                            className="nin-w-full nin-px-4 nin-py-2 nin-border nin-rounded-lg"
                        />
                    </div>
                    
                    <div>
                        <label className="nin-block nin-mb-2 nin-font-medium">Message</label>
                        <textarea
                            name="message"
                            value={formData.message}
                            onChange={handleChange}
                            required
                            rows="4"
                            className="nin-w-full nin-px-4 nin-py-2 nin-border nin-rounded-lg"
                        ></textarea>
                    </div>
                    
                    <button
                        type="submit"
                        disabled={submitting}
                        className="nin-w-full nin-px-6 nin-py-3 nin-bg-blue-600 nin-text-white nin-font-medium nin-rounded-lg hover:nin-bg-blue-700 disabled:nin-opacity-50"
                    >
                        {submitting ? 'Submitting...' : 'Submit'}
                    </button>
                </form>
            </div>
        </div>
    );
};

export default FrontendApp;
