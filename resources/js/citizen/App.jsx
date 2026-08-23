import { lazy, Suspense } from 'react';
import { Route, Routes } from 'react-router-dom';

const HomePage = lazy(() => import('./pages/HomePage'));
const CompleteGoogleRegistrationPage = lazy(() => import('./pages/CompleteGoogleRegistrationPage'));
const ApplyPage = lazy(() => import('./pages/ApplyPage'));
const LoginPage = lazy(() => import('./pages/LoginPage'));
const MyApplicationDetailPage = lazy(() => import('./pages/MyApplicationDetailPage'));
const MyApplicationsPage = lazy(() => import('./pages/MyApplicationsPage'));
const ProfilePage = lazy(() => import('./pages/ProfilePage'));
const RegisterPage = lazy(() => import('./pages/RegisterPage'));
const ServiceCatalog = lazy(() => import('./pages/ServiceCatalog'));
const ServiceDetail = lazy(() => import('./pages/ServiceDetail'));

function RouteFallback() {
    return <div className="flex min-h-[40vh] items-center justify-center py-20 text-sm text-gray-500">Đang tải...</div>;
}

export default function App() {
    return (
        <Suspense fallback={<RouteFallback />}>
            <Routes>
                <Route path="/login" element={<LoginPage />} />
                <Route path="/register" element={<RegisterPage />} />
                <Route path="/auth/google/complete" element={<CompleteGoogleRegistrationPage />} />
                <Route path="/profile" element={<ProfilePage />} />
                <Route path="/services" element={<ServiceCatalog />} />
                <Route path="/services/:id" element={<ServiceDetail />} />
                <Route path="/services/:id/apply" element={<ApplyPage />} />
                <Route path="/applications" element={<MyApplicationsPage />} />
                <Route path="/applications/:id" element={<MyApplicationDetailPage />} />
                <Route path="*" element={<HomePage />} />
            </Routes>
        </Suspense>
    );
}
