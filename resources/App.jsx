import Layout from './js/Components/Layout';
import Prontuario from './js/Pages/Prontuario';
import Dashboard from './js/Pages/Dashboard';

function App() {
  const page = window.APP_PAGE ?? 'dashboard';

  return (
    <Layout activePage={page}>
      {page === 'prontuario' ? <Prontuario /> : <Dashboard />}
    </Layout>
  );
}

export default App;