const fs = require('fs');
const axios = require('axios'); // Requires axios

const API_URL = 'https://meeting-logs-backend-b0x8.onrender.com/api/members';
const CSV_FILE = 'members.csv';

if (!fs.existsSync(CSV_FILE)) {
    console.error(`Không tìm thấy file ${CSV_FILE}`);
    process.exit(1);
}

const lines = fs.readFileSync(CSV_FILE, 'utf-8').split('\n');

async function syncMembers() {
    let count = 0;
    for (let i = 0; i < lines.length; i++) {
        let line = lines[i].trim();
        if (!line) continue;
        
        let parts = line.split(',');
        let name = parts[0].trim();
        let team = parts[1] ? parts[1].trim() : '';

        if (!name) continue;

        try {
            await axios.post(API_URL, { name, team });
            console.log(`Đã đồng bộ lên Web: ${name}`);
            count++;
        } catch (error) {
            console.error(`Lỗi khi đồng bộ ${name}:`, error.message);
        }
    }
    console.log(`Hoàn tất! Đã đưa ${count} nhân viên lên hệ thống Web chính thức.`);
}

syncMembers();
