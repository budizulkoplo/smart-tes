import { useEffect, useState } from 'react';
import Checkbox from '@/Components/Checkbox';
import GuestLayout from '@/Layouts/GuestLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm, usePage } from '@inertiajs/react';

export default function Login() {
    const { appSetting } = usePage().props;
    const [mode, setMode] = useState('login');
    const loginForm = useForm({
        nik: '',
        password: '',
        remember: false,
    });
    const registerForm = useForm({
        nik: '',
        name: '',
        no_hp: '',
        alamat: '',
    });

    useEffect(() => {
        return () => loginForm.reset('password');
    }, []);

    const submitLogin = (event) => {
        event.preventDefault();
        loginForm.post(route('student.login'));
    };

    const submitRegister = (event) => {
        event.preventDefault();
        registerForm.post(route('student.register'));
    };

    return (
        <GuestLayout>
            <Head title={`${appSetting?.app_name || 'SMART-TES'} - Login Calon Karyawan`} />

            <div className="mb-6">
                <p className="text-sm font-semibold uppercase tracking-[0.18em] text-teal-700">Tes calon karyawan</p>
                <h2 className="mt-2 text-2xl font-bold tracking-tight text-slate-900">
                    {mode === 'login' ? 'Masuk untuk mengerjakan tes' : 'Registrasi calon karyawan'}
                </h2>
            </div>

            <div className="mb-6 grid grid-cols-2 rounded-xl bg-slate-100 p-1">
                <button type="button" onClick={() => setMode('login')} className={`rounded-lg px-3 py-2 text-sm font-semibold ${mode === 'login' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-500'}`}>Masuk</button>
                <button type="button" onClick={() => setMode('register')} className={`rounded-lg px-3 py-2 text-sm font-semibold ${mode === 'register' ? 'bg-white text-teal-700 shadow-sm' : 'text-slate-500'}`}>Registrasi</button>
            </div>

            {mode === 'login' ? (
                <form onSubmit={submitLogin}>
                    <div>
                        <InputLabel htmlFor="nik" value="NIK" className="text-slate-700" />
                        <TextInput id="nik" type="text" inputMode="numeric" maxLength={16} name="nik" value={loginForm.data.nik} className="mt-1 block w-full" autoComplete="username" isFocused onChange={(event) => loginForm.setData('nik', event.target.value)} />
                        <InputError message={loginForm.errors.nik} className="mt-2" />
                    </div>
                    <div className="mt-4">
                        <InputLabel htmlFor="password" value="Password" className="text-slate-700" />
                        <TextInput id="password" type="password" name="password" value={loginForm.data.password} className="mt-1 block w-full" autoComplete="current-password" onChange={(event) => loginForm.setData('password', event.target.value)} />
                        <InputError message={loginForm.errors.password} className="mt-2" />
                    </div>
                    <div className="mt-4 block">
                        <label className="flex items-center">
                            <Checkbox name="remember" checked={loginForm.data.remember} onChange={(event) => loginForm.setData('remember', event.target.checked)} />
                            <span className="ms-2 text-sm text-slate-500">Ingat perangkat ini</span>
                        </label>
                    </div>
                    <PrimaryButton className="mt-6 w-full justify-center rounded-xl bg-teal-700 py-3 normal-case tracking-normal shadow-sm hover:bg-teal-800" disabled={loginForm.processing}>
                        {loginForm.processing ? 'Memproses...' : 'Masuk'}
                    </PrimaryButton>
                </form>
            ) : (
                <form onSubmit={submitRegister}>
                    <div>
                        <InputLabel htmlFor="register_nik" value="NIK" className="text-slate-700" />
                        <TextInput id="register_nik" type="text" inputMode="numeric" maxLength={16} name="nik" value={registerForm.data.nik} className="mt-1 block w-full" isFocused onChange={(event) => registerForm.setData('nik', event.target.value)} />
                        <InputError message={registerForm.errors.nik} className="mt-2" />
                    </div>
                    <div className="mt-4">
                        <InputLabel htmlFor="register_name" value="Nama Lengkap" className="text-slate-700" />
                        <TextInput id="register_name" type="text" name="name" value={registerForm.data.name} className="mt-1 block w-full" onChange={(event) => registerForm.setData('name', event.target.value)} />
                        <InputError message={registerForm.errors.name} className="mt-2" />
                    </div>
                    <div className="mt-4">
                        <InputLabel htmlFor="register_no_hp" value="No. HP" className="text-slate-700" />
                        <TextInput id="register_no_hp" type="tel" inputMode="tel" maxLength={20} name="no_hp" value={registerForm.data.no_hp} className="mt-1 block w-full" onChange={(event) => registerForm.setData('no_hp', event.target.value)} />
                        <InputError message={registerForm.errors.no_hp} className="mt-2" />
                    </div>
                    <div className="mt-4">
                        <InputLabel htmlFor="register_alamat" value="Alamat" className="text-slate-700" />
                        <textarea id="register_alamat" name="alamat" value={registerForm.data.alamat} rows="3" className="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-teal-500 focus:ring-teal-500" onChange={(event) => registerForm.setData('alamat', event.target.value)} />
                        <InputError message={registerForm.errors.alamat} className="mt-2" />
                    </div>
                    <div className="mt-4 rounded-lg border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-800">
                        Setelah registrasi, akun akan langsung aktif. Password awal otomatis adalah <strong>12345678</strong>.
                    </div>
                    <PrimaryButton className="mt-6 w-full justify-center rounded-xl bg-teal-700 py-3 normal-case tracking-normal shadow-sm hover:bg-teal-800" disabled={registerForm.processing}>
                        {registerForm.processing ? 'Memproses...' : 'Daftar & Masuk'}
                    </PrimaryButton>
                </form>
            )}
        </GuestLayout>
    );
}
