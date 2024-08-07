import Image from "next/image";
import logo from '../../public/logo-fdm.png'
import login from "../../public/login.png";
import Button from "@/components/button"

export default function Login(){
    return (
        <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-blue-800 to-black">
            <div className="bg-white shadow-lg rounded-[40px] overflow-hidden w-full max-w-lg flex flex-col md:flex-row mx-16 sm:mx-16">
                <div className="w-full px-8 py-16 flex flex-col items-center justify-center">
                    <div className="text-center mb-6">
                        <Image src={logo} alt="Logo" className="mx-auto mb-4 w-48 h-48 sm:w-40 sm:h-40 md:w-32 md:h-32 "/>
                    </div>
                    <form>
                        <div className="mb-4">
                            <label className="block text-gray-700 text-sm font-bold mb-2" htmlFor="username">
                                Usuario
                            </label>
                            <input
                            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:shadow-outline"
                            id="username"
                            type="text"
                            placeholder="Introduce tu usuario"
                            />
                        </div>
                        <div className="mb-6">
                            <label className="block text-gray-700 text-sm font-bold mb-2" htmlFor="password">
                                Contraseña
                            </label>
                            <input
                            className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:shadow-outline"
                            id="password"
                            type="password"
                            placeholder="Introduce tu contraseña"
                            />
                        </div>
                        <div className="flex items-center justify-between">
                            <Button text="Iniciar Sesión"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    );
}