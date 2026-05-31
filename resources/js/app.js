import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Mock database initial state
const defaultStudents = [
    { id: 1, nis: "12903841", name: "Aditya Pratama", gender: "L", classroom: "IX-A", status: "Lengkap" },
    { id: 2, nis: "12903842", name: "Bunga Citra Lestari", gender: "P", classroom: "IX-C", status: "Belum Lengkap" },
    { id: 3, nis: "12903845", name: "Cahyo Rahmadi", gender: "L", classroom: "IX-A", status: "Lengkap" },
    { id: 4, nis: "12903848", name: "Dewi Sartika", gender: "P", classroom: "IX-B", status: "Lengkap" },
    { id: 5, nis: "12903850", name: "Eka Wijaya", gender: "L", classroom: "IX-D", status: "Belum Lengkap" },
    { id: 6, nis: "12903855", name: "Fajar Ramadhan", gender: "L", classroom: "IX-A", status: "Lengkap" },
    { id: 7, nis: "12903859", name: "Giska Amanda", gender: "P", classroom: "IX-C", status: "Lengkap" },
    { id: 8, nis: "12903862", name: "Hendra Kurnia", gender: "L", classroom: "IX-B", status: "Lengkap" },
    { id: 9, nis: "12903868", name: "Indah Permata", gender: "P", classroom: "IX-A", status: "Belum Lengkap" },
    { id: 10, nis: "12903875", name: "Joko Susilo", gender: "L", classroom: "IX-D", status: "Lengkap" }
];

const defaultScores = {
    // Student ID: { semester_id: { subject: score } }
    1: { 1: { matematika: 88, ipa: 85, bahasa_indonesia: 80, bahasa_inggris: 82, ips: 75, seni_budaya: 78 } },
    3: { 1: { matematika: 90, ipa: 92, bahasa_indonesia: 85, bahasa_inggris: 86, ips: 70, seni_budaya: 72 } },
    4: { 1: { matematika: 75, ipa: 78, bahasa_indonesia: 88, bahasa_inggris: 90, ips: 82, seni_budaya: 80 } },
    6: { 1: { matematika: 85, ipa: 80, bahasa_indonesia: 82, bahasa_inggris: 85, ips: 78, seni_budaya: 88 } },
    7: { 1: { matematika: 70, ipa: 72, bahasa_indonesia: 90, bahasa_inggris: 92, ips: 75, seni_budaya: 82 } },
    8: { 1: { matematika: 80, ipa: 78, bahasa_indonesia: 80, bahasa_inggris: 78, ips: 85, seni_budaya: 75 } },
    10: { 1: { matematika: 82, ipa: 84, bahasa_indonesia: 80, bahasa_inggris: 80, ips: 72, seni_budaya: 92 } }
};

const defaultAnswers = {
    // Student ID: { question_id: LikertScale 1-5 }
    1: { 1: 5, 2: 4, 3: 2, 4: 3, 5: 2, 6: 4 },
    3: { 1: 5, 2: 5, 3: 2, 4: 2, 5: 3, 6: 3 },
    4: { 1: 2, 2: 3, 3: 4, 4: 5, 5: 3, 6: 2 },
    6: { 1: 3, 2: 3, 3: 2, 4: 2, 5: 5, 6: 5 },
    7: { 1: 2, 2: 2, 3: 5, 4: 4, 5: 3, 6: 3 },
    8: { 1: 3, 2: 3, 3: 4, 4: 4, 5: 2, 6: 3 },
    10: { 1: 4, 2: 4, 3: 3, 4: 2, 5: 5, 6: 4 }
};

const defaultQuestions = [
    { id: 1, question: "Saya senang memecahkan soal matematika atau teka-teki logika.", category: "IPA", weight: 0.8 },
    { id: 2, question: "Saya tertarik melakukan eksperimen sains atau mempelajari gejala alam.", category: "IPA", weight: 0.8 },
    { id: 3, question: "Saya menikmati diskusi masalah sosial, sejarah, dan politik.", category: "IPS", weight: 0.8 },
    { id: 4, question: "Saya senang membaca novel, menulis puisi, atau mempelajari bahasa asing.", category: "Bahasa", weight: 0.8 },
    { id: 5, question: "Saya suka merakit alat, memperbaiki sirkuit elektronik, atau coding.", category: "Vokasi", weight: 0.8 },
    { id: 6, question: "Saya senang menggambar, mendesain produk, atau membuat kerajinan tangan.", category: "Vokasi", weight: 0.6 }
];

const defaultModel = {
    version: "v1.2",
    accuracy: 87.5,
    precision: 86.8,
    recall: 85.9,
    f1: 86.3,
    confusionMatrix: {
        ipa: { ipa: 24, ips: 2, bahasa: 1, vokasi: 1 },
        ips: { ipa: 1, ips: 20, bahasa: 2, vokasi: 1 },
        bahasa: { ipa: 0, ips: 3, bahasa: 12, vokasi: 1 },
        vokasi: { ipa: 2, ips: 1, bahasa: 1, vokasi: 18 }
    }
};

const defaultResults = [
    { id: 1, studentId: 1, path: "IPA", ipa: 0.75, ips: 0.15, bahasa: 0.05, vokasi: 0.05, factors: "Nilai Matematika dan IPA yang konsisten tinggi, dipadu dengan ketertarikan tinggi pada logika eksakta." },
    { id: 2, studentId: 3, path: "IPA", ipa: 0.82, ips: 0.10, bahasa: 0.04, vokasi: 0.04, factors: "Hasil kognitif sains di atas KKM secara konsisten dan kecenderungan minat sains kuat." },
    { id: 3, studentId: 4, path: "Bahasa", ipa: 0.08, ips: 0.22, bahasa: 0.62, vokasi: 0.08, factors: "Skor Bahasa Inggris dan Bahasa Indonesia sangat dominan di atas rata-rata kelas." },
    { id: 4, studentId: 6, path: "Vokasi", ipa: 0.18, ips: 0.12, bahasa: 0.10, vokasi: 0.60, factors: "Ketrampilan praktek dan minat rekayasa/teknik yang menonjol pada kuesioner minat." },
    { id: 5, studentId: 7, path: "Bahasa", ipa: 0.05, ips: 0.15, bahasa: 0.72, vokasi: 0.08, factors: "Minat linguistik sastra sangat kuat dikombinasikan dengan performa bahasa unggul." },
    { id: 6, studentId: 8, path: "IPS", ipa: 0.10, ips: 0.68, bahasa: 0.12, vokasi: 0.10, factors: "Nilai IPS tinggi ditunjang ketertarikan kuat dalam berdiskusi fenomena kemasyarakatan." },
    { id: 7, studentId: 10, path: "Vokasi", ipa: 0.20, ips: 0.10, bahasa: 0.10, vokasi: 0.60, factors: "Nilai seni budaya tinggi dan memiliki ketertarikan kerajinan tangan praktek." }
];

// Alpine.store registration
Alpine.store('app', {
    students: JSON.parse(localStorage.getItem('smp_students')) || defaultStudents,
    scores: JSON.parse(localStorage.getItem('smp_scores')) || defaultScores,
    answers: JSON.parse(localStorage.getItem('smp_answers')) || defaultAnswers,
    questions: JSON.parse(localStorage.getItem('smp_questions')) || defaultQuestions,
    model: JSON.parse(localStorage.getItem('smp_model')) || defaultModel,
    results: JSON.parse(localStorage.getItem('smp_results')) || defaultResults,
    currentRole: localStorage.getItem('smp_role') || 'admin',
    currentSearch: '',

    saveAll() {
        localStorage.setItem('smp_students', JSON.stringify(this.students));
        localStorage.setItem('smp_scores', JSON.stringify(this.scores));
        localStorage.setItem('smp_answers', JSON.stringify(this.answers));
        localStorage.setItem('smp_questions', JSON.stringify(this.questions));
        localStorage.setItem('smp_model', JSON.stringify(this.model));
        localStorage.setItem('smp_results', JSON.stringify(this.results));
    },

    setRole(role) {
        this.currentRole = role;
        localStorage.setItem('smp_role', role);
    },

    // CRUD Students
    addStudent(student) {
        const id = this.students.length ? Math.max(...this.students.map(s => s.id)) + 1 : 1;
        const newStudent = { id, status: "Belum Lengkap", ...student };
        this.students.push(newStudent);
        this.saveAll();
        return newStudent;
    },

    editStudent(id, updatedData) {
        const idx = this.students.findIndex(s => s.id === id);
        if (idx !== -1) {
            this.students[idx] = { ...this.students[idx], ...updatedData };
            this.saveAll();
        }
    },

    deleteStudent(id) {
        this.students = this.students.filter(s => s.id !== id);
        delete this.scores[id];
        delete this.answers[id];
        this.results = this.results.filter(r => r.studentId !== id);
        this.saveAll();
    },

    // Save Student Score
    saveScore(studentId, semesterId, scoreData) {
        if (!this.scores[studentId]) this.scores[studentId] = {};
        this.scores[studentId][semesterId] = scoreData;
        this.updateStudentStatus(studentId);
        this.saveAll();
    },

    // Save Questionnaire Answers
    saveAnswers(studentId, answerData) {
        this.answers[studentId] = answerData;
        this.updateStudentStatus(studentId);
        this.saveAll();
    },

    updateStudentStatus(studentId) {
        const score = this.scores[studentId] && this.scores[studentId][1]; // Sem 1 for simplicity
        const ans = this.answers[studentId];
        const student = this.students.find(s => s.id == studentId);
        
        if (student) {
            const hasScores = score && Object.keys(score).length >= 6;
            const hasAnswers = ans && Object.keys(ans).length >= this.questions.length;
            
            if (hasScores && hasAnswers) {
                student.status = "Lengkap";
            } else {
                student.status = "Belum Lengkap";
            }
        }
    },

    // Simulated Naive Bayes classification calculator
    classifyStudent(studentId) {
        const score = this.scores[studentId] && this.scores[studentId][1];
        const ans = this.answers[studentId];
        
        if (!score || !ans) return null;

        // 1. Calculate Academic Tendency
        const mat = Number(score.matematika || 0);
        const ipa = Number(score.ipa || 0);
        const ips = Number(score.ips || 0);
        const ind = Number(score.bahasa_indonesia || 0);
        const ing = Number(score.bahasa_inggris || 0);
        const seni = Number(score.seni_budaya || 0);

        // 2. Sum Questionnaire Answers per Category
        let ipaMinat = 0, ipsMinat = 0, bahasaMinat = 0, vokasiMinat = 0;
        this.questions.forEach(q => {
            const val = Number(ans[q.id] || 3); // neutral is 3
            if (q.category === 'IPA') ipaMinat += val;
            else if (q.category === 'IPS') ipsMinat += val;
            else if (q.category === 'Bahasa') bahasaMinat += val;
            else if (q.category === 'Vokasi') vokasiMinat += val;
        });

        // 3. Mathematical Probability scoring based on rules
        let ipaScore = (mat * 1.2 + ipa * 1.2 + (ipaMinat * 10)) * 1.5;
        let ipsScore = (ips * 1.3 + (ipsMinat * 10)) * 1.3;
        let bahasaScore = (ind * 1.1 + ing * 1.2 + (bahasaMinat * 10)) * 1.2;
        let vokasiScore = (seni * 1.1 + ((mat + ipa) * 0.4) + (vokasiMinat * 10)) * 1.1;

        // Add some random Gaussian-like noise for realism
        ipaScore += (Math.random() - 0.5) * 15;
        ipsScore += (Math.random() - 0.5) * 15;
        bahasaScore += (Math.random() - 0.5) * 15;
        vokasiScore += (Math.random() - 0.5) * 15;

        // Apply weights
        const total = ipaScore + ipsScore + bahasaScore + vokasiScore;
        const pIpa = Math.max(0.01, ipaScore / total);
        const pIps = Math.max(0.01, ipsScore / total);
        const pBahasa = Math.max(0.01, bahasaScore / total);
        const pVokasi = Math.max(0.01, vokasiScore / total);

        // Determine major
        let path = "IPA";
        let maxProb = pIpa;
        if (pIps > maxProb) { path = "IPS"; maxProb = pIps; }
        if (pBahasa > maxProb) { path = "Bahasa"; maxProb = pBahasa; }
        if (pVokasi > maxProb) { path = "Vokasi"; maxProb = pVokasi; }

        // Generate dominant factors explanation
        let factors = "";
        if (path === "IPA") {
            factors = `Nilai eksakta (${Math.max(mat, ipa)} pada Matematika/IPA) sangat konsisten di atas KKM, dipadukan dengan minat sains ${ipaMinat >= 8 ? 'kuat' : 'cukup'} dari kuesioner bakat.`;
        } else if (path === "IPS") {
            factors = `Performansi sosial-humaniora (${ips} pada IPS) menonjol dikombinasikan dengan minat interaksi kemasyarakatan sebesar ${ipsMinat}/10.`;
        } else if (path === "Bahasa") {
            factors = `Kemampuan linguistik verbal (${Math.max(ind, ing)} pada Bahasa) di atas rata-rata dan kecenderungan minat sastra yang kuat.`;
        } else {
            factors = `Pencapaian seni budaya (${seni}) serta ketrampilan rekayasa terapan praktis sangat tinggi pada hasil kuesioner.`;
        }

        // Add or update results list
        const existingIdx = this.results.findIndex(r => r.studentId == studentId);
        const resObj = {
            id: existingIdx !== -1 ? this.results[existingIdx].id : Math.random(),
            studentId: Number(studentId),
            path,
            ipa: Number(pIpa.toFixed(2)),
            ips: Number(pIps.toFixed(2)),
            bahasa: Number(pBahasa.toFixed(2)),
            vokasi: Number(pVokasi.toFixed(2)),
            factors
        };

        if (existingIdx !== -1) {
            this.results[existingIdx] = resObj;
        } else {
            this.results.push(resObj);
        }
        this.saveAll();
        return resObj;
    },

    // Batch Classification
    classifyAllComplete() {
        let count = 0;
        this.students.forEach(s => {
            if (s.status === 'Lengkap') {
                this.classifyStudent(s.id);
                count++;
            }
        });
        return count;
    },

    // Train Model
    trainModel() {
        // Boost metrics slightly and update version
        const vNum = parseFloat(this.model.version.replace('v', '')) + 0.1;
        this.model.version = `v${vNum.toFixed(1)}`;
        this.model.accuracy = Number((85 + Math.random() * 5).toFixed(1));
        this.model.precision = Number((84 + Math.random() * 5).toFixed(1));
        this.model.recall = Number((84 + Math.random() * 5).toFixed(1));
        
        // Add random shifts to confusion matrix
        Object.keys(this.model.confusionMatrix).forEach(row => {
            Object.keys(this.model.confusionMatrix[row]).forEach(col => {
                const shift = Math.floor(Math.random() * 3) - 1; // -1, 0, or 1
                if (row === col) {
                    this.model.confusionMatrix[row][col] = Math.max(10, this.model.confusionMatrix[row][col] + Math.abs(shift));
                } else {
                    this.model.confusionMatrix[row][col] = Math.max(0, this.model.confusionMatrix[row][col] + shift);
                }
            });
        });
        
        this.saveAll();
    }
});

Alpine.start();
